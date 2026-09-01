import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { phpRuntimeArgs } from './php.mjs';
import { projectRoot } from './project.mjs';

export const qaDatabasePath = path.join(projectRoot, 'tests', '.artifacts', 'qa.sqlite');

/**
 * Read rows from the disposable SQLite database created by the managed QA
 * server. SQL and parameters are passed as separate CLI arguments so fixture
 * values cannot become executable PHP source.
 */
export function queryQaDatabase(sql, parameters = {}) {
  const php = String.raw`
    $databasePath = $argv[1];
    $sql = $argv[2];
    $parameters = json_decode($argv[3], true, 512, JSON_THROW_ON_ERROR);
    $pdo = new PDO('sqlite:' . $databasePath, null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $statement = $pdo->prepare($sql);
    $statement->execute($parameters);
    echo json_encode($statement->fetchAll(), JSON_THROW_ON_ERROR);
  `;

  const output = execFileSync(
    'php',
    [
      ...phpRuntimeArgs(),
      '-r',
      php,
      qaDatabasePath,
      sql,
      JSON.stringify(parameters),
    ],
    {
      cwd: projectRoot,
      encoding: 'utf8',
      windowsHide: true,
    },
  );

  return JSON.parse(output);
}

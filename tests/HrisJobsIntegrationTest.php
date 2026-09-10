<?php
declare(strict_types=1);
$failures=[];$check=static function(bool $condition,string $message)use(&$failures):void{if(!$condition){$failures[]=$message;echo "FAIL: {$message}\n";}else echo "PASS: {$message}\n";};
$root=realpath(__DIR__.'/..');$adapter=(string)file_get_contents($root.'/app/hris_jobs.php');$recruitment=(string)file_get_contents($root.'/app/recruitment.php');$detail=(string)file_get_contents($root.'/careers/job.php');
$check(str_contains($adapter,"HRIS_JOBS_FEED_ENABLED"),'HRIS job source is opt-in');
$check(str_contains($adapter,"preg_match('#^https://"),'HRIS integration requires HTTPS');
$check(str_contains($adapter,"schema_version"),'HRIS feed validates its schema version');
$check(str_contains($adapter, "'stored_at'") && preg_match('/<=\s*900/', $adapter) === 1,'HRIS feed has a bounded stale-if-error cache');
$check(str_contains($adapter,"public_id"),'HRIS feed validates public job identifiers');
$check(!str_contains($adapter,'HRIS_CANDIDATE_BASE_URL'),'Candidate URLs are not configured on the HRIS host');
$check(str_contains($adapter,"'apply_url' => '/apply/'"),'Apply handoff remains on the TAASCOR public domain');
$check(str_contains($recruitment, 'if (hris_jobs_feed_enabled())') && str_contains($recruitment, 'return hris_published_jobs();'),'Careers uses HRIS as the single source when enabled');
$check(str_contains($detail,"['apply_url']"),'Job detail uses the same-origin candidate handoff');
$check(str_contains($detail,'/account/login.php?next='),'Candidate sign in remains on the TAASCOR public domain');
$check(!str_contains($detail,'visiotechsolutions.com'),'Job detail never exposes the HRIS hostname');
$check(!str_contains($adapter,'APPLICANT_COLLECTION'),'Website adapter never collects applicant data');
if($failures!==[]){fwrite(STDERR,"RESULT: ".count($failures)." HRIS integration check(s) failed.\n");exit(1);} echo "RESULT: HRIS jobs integration checks passed.\n";

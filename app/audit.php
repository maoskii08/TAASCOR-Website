<?php

declare(strict_types=1);

/** @param array<string, scalar|null> $metadata */
function audit_event(
    string $eventType,
    string $entityType,
    ?int $entityId = null,
    array $metadata = [],
    ?int $actorUserId = null,
    bool $required = true
): void {
    try {
        $actorUserId ??= isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $statement = db()->prepare(
            'INSERT INTO audit_events
                (actor_user_id, event_type, entity_type, entity_id, metadata_json, ip_hash, user_agent_hash, created_at)
             VALUES
                (:actor_user_id, :event_type, :entity_type, :entity_id, :metadata_json, :ip_hash, :user_agent_hash, :created_at)'
        );
        $statement->execute([
            'actor_user_id' => $actorUserId,
            'event_type' => mb_substr($eventType, 0, 100),
            'entity_type' => mb_substr($entityType, 0, 100),
            'entity_id' => $entityId,
            'metadata_json' => json_encode($metadata, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES),
            'ip_hash' => request_fingerprint((string) ($_SERVER['REMOTE_ADDR'] ?? 'cli')),
            'user_agent_hash' => request_fingerprint((string) ($_SERVER['HTTP_USER_AGENT'] ?? 'cli')),
            'created_at' => now_utc(),
        ]);
    } catch (Throwable $exception) {
        error_log('TAASCOR audit event failure: ' . $exception->getMessage());
        if ($required) {
            throw new RuntimeException('The required audit record could not be written.', 0, $exception);
        }
    }
}

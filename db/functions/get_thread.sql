DROP FUNCTION IF EXISTS get_thread;

CREATE OR REPLACE FUNCTION get_thread(id INT)
    RETURNS TABLE
            (
                post_id       INT,
                creation_time TIMESTAMP,
                mentioned     json,
                name          VARCHAR(32),
                subject       VARCHAR(64),
                comment       VARCHAR(1024),
                image         TEXT
            )
AS
$$

DECLARE
    thread_id INT;
BEGIN
    -- If the post doesn't have a parent
    IF (SELECT p.parent_id FROM posts p WHERE p.post_id = id) IS NULL THEN
        thread_id := (SELECT p.post_id FROM posts p WHERE p.post_id = id);
    ELSE -- If the post has a parent, get the parent
        thread_id := (SELECT p.parent_id FROM posts p WHERE p.post_id = id);
    END IF;

    RETURN QUERY
        SELECT p.post_id,
               p.creation_time,
               array_to_json(p.mentioned),
               p.name,
               p.subject,
               p.comment,
               p.image
        FROM posts p
        WHERE p.post_id = thread_id
           OR p.parent_id = thread_id
        ORDER BY p.post_id;
END

$$
    LANGUAGE plpgsql;
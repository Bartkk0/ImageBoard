DROP PROCEDURE IF EXISTS bump_thread;

CREATE PROCEDURE bump_thread(thread_id INT)
    LANGUAGE sql AS
$$
UPDATE posts
SET bump_count = bump_count + 1,
    bump_time  = CURRENT_TIMESTAMP
WHERE posts.post_id = thread_id
  AND posts.bump_count < 5;
$$
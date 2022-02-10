CREATE OR REPLACE PROCEDURE clear_old_posts()
    LANGUAGE plpgsql
AS
$$

DECLARE
    safe_posts INTEGER[];
    to_delete  INTEGER[];

BEGIN

    safe_posts := ARRAY(SELECT post_id
                        FROM posts
                        WHERE parent_id IS NULL
                        ORDER BY bump_time
                        LIMIT 10);

    to_delete := ARRAY(SELECT post_id
                       FROM posts
                       WHERE NOT (post_id = ANY (safe_posts))
                          OR NOT (parent_id = ANY (safe_posts)));

    DELETE FROM posts WHERE (post_id = ANY (to_delete));
END

$$;


DROP PROCEDURE IF EXISTS create_reply;

CREATE PROCEDURE create_reply(IN in_name VARCHAR, in_parent INTEGER, IN in_subject VARCHAR,
                              IN in_comment VARCHAR, IN in_image TEXT)
    LANGUAGE plpgsql
AS
$$
DECLARE
    _mentions INT[];
    m        INT;
BEGIN
    -- Add a new record
    INSERT INTO posts(name, parent_id, subject, comment, image)
    VALUES (in_name, in_parent, in_subject, in_comment, in_image);

    -- Get mentions in the comment
    SELECT ARRAY(SELECT REGEXP_MATCHES(in_comment, '>>(\d*)', 'gm'))::INTEGER[] INTO _mentions;
    _mentions := ARRAY(SELECT UNNEST(_mentions));

    RAISE NOTICE '%', _mentions;


    FOREACH m IN ARRAY ARRAY(SELECT post_id FROM posts WHERE post_id = ANY (_mentions))
        LOOP
            UPDATE posts p SET mentioned = array_append(p.mentioned, lastval()::INT) WHERE p.post_id = m;
        END LOOP;
END
$$;

CALL create_reply('Anonymous', NULL, 'Test', '>>1' ||
                                             '>>2' ||
                                             '>>23134' ||
                                             '>sdadasdad' ||
                                             'wdasdasda', '');

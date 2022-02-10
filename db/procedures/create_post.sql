CREATE OR REPLACE PROCEDURE public.create_post(IN in_name varchar, IN in_subject varchar, IN in_comment varchar, IN in_image text)
    LANGUAGE sql
    SECURITY INVOKER
AS
$$
INSERT INTO posts(name, subject, comment, image)
VALUES (in_name, in_subject, in_comment, in_image);
$$;

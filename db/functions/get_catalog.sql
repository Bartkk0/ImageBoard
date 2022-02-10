DROP FUNCTION IF EXISTS get_catalog;

CREATE OR REPLACE FUNCTION get_catalog()
    RETURNS TABLE
            (
                post_id INT,
                name    VARCHAR(32),
                subject VARCHAR(64),
                image   TEXT,
                replies INT,
                images  INT
            )
AS
$$

SELECT p.post_id,
       p.name,
       p.subject,
       p.image,
       (SELECT CAST(COUNT(*) AS INTEGER) FROM posts c WHERE c.parent_id = p.post_id) AS replies,
       (SELECT CAST(COUNT(*) AS INTEGER)
        FROM posts c
        WHERE c.parent_id = p.post_id
          AND c.image IS NOT NULL)                                                   AS images
FROM posts p
WHERE p.parent_id IS NULL
ORDER BY p.bump_time DESC

$$
    LANGUAGE sql;
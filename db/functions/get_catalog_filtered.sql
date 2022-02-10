DROP FUNCTION IF EXISTS get_catalog_filtered;

CREATE OR REPLACE FUNCTION get_catalog_filtered(filter TEXT)
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
SELECT *
FROM get_catalog() c
WHERE STRPOS(lower(c.subject), lower(filter)) > 0;
$$
    LANGUAGE sql;
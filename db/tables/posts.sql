DROP TABLE IF EXISTS posts CASCADE;

CREATE TABLE posts
(
    post_id       SERIAL    NOT NULL,
    parent_id     INTEGER            DEFAULT NULL,
    creation_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bump_time     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bump_count    INT                DEFAULT 0,
    mentioned     INT[],
    name          VARCHAR(32)        DEFAULT 'Anonymous',
    subject       VARCHAR(64),
    comment       VARCHAR(1024),
    image         TEXT               DEFAULT NULL,
    CONSTRAINT posts_pk PRIMARY KEY (post_id, bump_time)
);
--
-- PostgreSQL database dump
--

-- Dumped from database version 14.2
-- Dumped by pg_dump version 14.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: mublog; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA mublog;


--
-- Name: SCHEMA mublog; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA mublog IS 'mublog.site database';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: access_levels; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.access_levels (
    alid smallint NOT NULL,
    label character varying(32) NOT NULL
);


--
-- Name: TABLE access_levels; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON TABLE mublog.access_levels IS 'access levels for users';


--
-- Name: COLUMN access_levels.label; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.access_levels.label IS 'Access level label';


--
-- Name: access_levels_alid_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.access_levels_alid_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: access_levels_alid_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.access_levels_alid_seq OWNED BY mublog.access_levels.alid;


--
-- Name: article_comments; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.article_comments (
    aid smallint NOT NULL,
    cid integer NOT NULL,
    deleted boolean DEFAULT false NOT NULL
);


--
-- Name: articles; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.articles (
    aid smallint NOT NULL,
    title character varying(256) NOT NULL,
    summary character varying(512) NOT NULL,
    body text NOT NULL,
    alias character varying(256) NOT NULL,
    created integer DEFAULT floor(EXTRACT(epoch FROM now())) NOT NULL,
    updated integer DEFAULT floor(EXTRACT(epoch FROM now())) NOT NULL,
    status boolean DEFAULT true NOT NULL,
    preview_src character varying(256) DEFAULT '/images/article-preview-default.png'::character varying,
    preview_alt character varying(256) DEFAULT NULL::character varying,
    author character varying(50) DEFAULT 'mublog.site'::character varying,
    views smallint DEFAULT 0 NOT NULL
);


--
-- Name: COLUMN articles.created; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.articles.created IS 'Published unix timestamp';


--
-- Name: COLUMN articles.updated; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.articles.updated IS 'Last article update unix timestamp';


--
-- Name: COLUMN articles.status; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.articles.status IS 'Published status';


--
-- Name: COLUMN articles.views; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.articles.views IS 'Number of article views';


--
-- Name: articles_id_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.articles_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: articles_id_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.articles_id_seq OWNED BY mublog.articles.aid;


--
-- Name: comments; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.comments (
    cid integer NOT NULL,
    pid integer,
    created integer DEFAULT floor(EXTRACT(epoch FROM now())) NOT NULL,
    name character varying(60) NOT NULL,
    email character varying(255) NOT NULL,
    body text NOT NULL,
    status boolean DEFAULT false NOT NULL,
    ip inet DEFAULT '0.0.0.0'::inet NOT NULL
);


--
-- Name: comments_cid_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.comments_cid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: comments_cid_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.comments_cid_seq OWNED BY mublog.comments.cid;


--
-- Name: feedbacks; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.feedbacks (
    fbid smallint NOT NULL,
    subject character varying(255) NOT NULL,
    message text NOT NULL,
    "timestamp" integer DEFAULT floor(EXTRACT(epoch FROM now())) NOT NULL,
    headers json NOT NULL,
    result boolean DEFAULT false NOT NULL
);


--
-- Name: feedbacks_id_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.feedbacks_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feedbacks_id_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.feedbacks_id_seq OWNED BY mublog.feedbacks.fbid;


--
-- Name: user_status_access_levels; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.user_status_access_levels (
    usid smallint NOT NULL,
    alid smallint NOT NULL
);


--
-- Name: user_status_access_levels_alid_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.user_status_access_levels_alid_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_status_access_levels_alid_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.user_status_access_levels_alid_seq OWNED BY mublog.user_status_access_levels.alid;


--
-- Name: user_status_access_levels_usid_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.user_status_access_levels_usid_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_status_access_levels_usid_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.user_status_access_levels_usid_seq OWNED BY mublog.user_status_access_levels.usid;


--
-- Name: users; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.users (
    uid smallint NOT NULL,
    mail character varying(150) NOT NULL,
    pwhash character varying(255) NOT NULL,
    nickname character(32) NOT NULL,
    registered integer DEFAULT floor(EXTRACT(epoch FROM now())) NOT NULL,
    usid smallint DEFAULT 2
);


--
-- Name: COLUMN users.uid; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.users.uid IS 'User status id. Default status is "Registered user"';


--
-- Name: users_sessions; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.users_sessions (
    uid smallint NOT NULL,
    token character(32) NOT NULL,
    agent_hash character(32) NOT NULL,
    browser character varying(50) NOT NULL,
    platform character varying(50) NOT NULL,
    updated integer DEFAULT floor(EXTRACT(epoch FROM now())) NOT NULL,
    ip inet DEFAULT '0.0.0.0'::inet NOT NULL
);


--
-- Name: TABLE users_sessions; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON TABLE mublog.users_sessions IS 'Users authorized sessions and devices';


--
-- Name: COLUMN users_sessions.uid; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.users_sessions.uid IS 'User unique id';


--
-- Name: COLUMN users_sessions.token; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.users_sessions.token IS 'User session token';


--
-- Name: COLUMN users_sessions.agent_hash; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.users_sessions.agent_hash IS 'User agent md5 hash';


--
-- Name: COLUMN users_sessions.updated; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.users_sessions.updated IS 'Users'' last action timestamp';


--
-- Name: COLUMN users_sessions.ip; Type: COMMENT; Schema: mublog; Owner: -
--

COMMENT ON COLUMN mublog.users_sessions.ip IS 'User session last ip address';


--
-- Name: users_statuses; Type: TABLE; Schema: mublog; Owner: -
--

CREATE TABLE mublog.users_statuses (
    usid smallint NOT NULL,
    status character(10) NOT NULL,
    label character varying(24) NOT NULL
);


--
-- Name: users_statuses_usid_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.users_statuses_usid_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_statuses_usid_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.users_statuses_usid_seq OWNED BY mublog.users_statuses.usid;


--
-- Name: users_uid_seq; Type: SEQUENCE; Schema: mublog; Owner: -
--

CREATE SEQUENCE mublog.users_uid_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_uid_seq; Type: SEQUENCE OWNED BY; Schema: mublog; Owner: -
--

ALTER SEQUENCE mublog.users_uid_seq OWNED BY mublog.users.uid;


--
-- Name: access_levels alid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.access_levels ALTER COLUMN alid SET DEFAULT nextval('mublog.access_levels_alid_seq'::regclass);


--
-- Name: articles aid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.articles ALTER COLUMN aid SET DEFAULT nextval('mublog.articles_id_seq'::regclass);


--
-- Name: comments cid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.comments ALTER COLUMN cid SET DEFAULT nextval('mublog.comments_cid_seq'::regclass);


--
-- Name: feedbacks fbid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.feedbacks ALTER COLUMN fbid SET DEFAULT nextval('mublog.feedbacks_id_seq'::regclass);


--
-- Name: user_status_access_levels usid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.user_status_access_levels ALTER COLUMN usid SET DEFAULT nextval('mublog.user_status_access_levels_usid_seq'::regclass);


--
-- Name: user_status_access_levels alid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.user_status_access_levels ALTER COLUMN alid SET DEFAULT nextval('mublog.user_status_access_levels_alid_seq'::regclass);


--
-- Name: users uid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.users ALTER COLUMN uid SET DEFAULT nextval('mublog.users_uid_seq'::regclass);


--
-- Name: users_statuses usid; Type: DEFAULT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.users_statuses ALTER COLUMN usid SET DEFAULT nextval('mublog.users_statuses_usid_seq'::regclass);


--
-- Data for Name: access_levels; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.access_levels (alid, label) FROM stdin;
1	For guests and anonymous users
2	All users
3	For registered users
4	For admins and webmasters
5	Only for webmasters
\.


--
-- Data for Name: article_comments; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.article_comments (aid, cid, deleted) FROM stdin;
\.


--
-- Data for Name: articles; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.articles (aid, title, summary, body, alias, created, updated, status, preview_src, preview_alt, author, views) FROM stdin;
1	Набор полезных навыков для веб-разработчика от Андреаса Мэлсена	Веб-разработчик из Германии Андреас Мэлсен создал сайт, на котором он собрал множество полезных инcтрументов для веб-разработчиков и поделился с нами.	&lt;p&gt;Веб-разработчик из Германии &lt;a href=&quot;https://andreasbm.github.io/&quot; target=&quot;_blank&quot;&gt;Андреас Мэлсен&lt;/a&gt; создал очень полезный сайт, &lt;a href=&quot;https://andreasbm.github.io/web-skills/&quot; target=&quot;_blank&quot;&gt;Web Skills&lt;/a&gt;, на котором он собрал и представил множество полезных ссылок на документации и статьи для изучения различных инструментов, которые могут очень пригодиться и быть полезными в веб-разработке.&lt;/p&gt;\\r\\n\\r\\n&lt;a href=&quot;https://andreasbm.github.io/web-skills/&quot; target=&quot;_blank&quot;&gt;\\r\\n    &lt;img src=&quot;/images/content/220210-image-web-skills-demo.gif&quot; alt=&quot;Web Skills Demo&quot; width=&quot;800&quot; height=&quot;512&quot;&gt;\\r\\n&lt;/a&gt;\\r\\n\\r\\n&lt;p&gt;На данном сайте выполнено визуальное представление полезных навыков по мнению Андреаса. Он рассказывает, что собранные им инструменты - результат 10-летнего опыта в веб-разработке и с которыми он, так или иначе, сталкивался лично. Новичкам же автор не рекомендует воспринимать свою библиотеку знаний, как руководство к изучению всего, что там представлено, а воспринимать это скорее как то, что возможно им придётся изучить в будущем. Так же Андреас утверждает, что на его сайте собрано гораздо больше, чем может потребоваться в повседневной разработке проектов, по этому не стоит пугаться столь огромного количества представленных материалов.&lt;/p&gt;	nabor-poleznih-navikov-dlya-veb-razrabotchika-ot-andreasa-melsena	1644451336	1644452710	t	/images/content/220210-preview-01.jpg	Web Skills	mublog.site	0
\.


--
-- Data for Name: comments; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.comments (cid, pid, created, name, email, body, status, ip) FROM stdin;
\.


--
-- Data for Name: feedbacks; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.feedbacks (fbid, subject, message, "timestamp", headers, result) FROM stdin;
\.


--
-- Data for Name: user_status_access_levels; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.user_status_access_levels (usid, alid) FROM stdin;
1	1
1	2
2	2
2	3
3	2
3	3
3	4
4	2
4	3
4	4
4	5
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.users (uid, mail, pwhash, nickname, registered, usid) FROM stdin;
1	admin@mublog.site	$argon2i$v=19$m=65536,t=4,p=1$RUc1TnhjQmFEdklMOUxxbA$Jqty69JewOT3ybQR1eUGEDkFP14vRAQVm/vZcB31T2M	mublog.site                     	0	4
\.


--
-- Data for Name: users_sessions; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.users_sessions (uid, token, agent_hash, browser, platform, updated, ip) FROM stdin;
\.


--
-- Data for Name: users_statuses; Type: TABLE DATA; Schema: mublog; Owner: -
--

COPY mublog.users_statuses (usid, status, label) FROM stdin;
2	user      	Registered user
4	master    	Webmaster
1	anonym    	Anonymous user
3	admin     	Administrator
\.


--
-- Name: access_levels_alid_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.access_levels_alid_seq', 1, false);


--
-- Name: articles_id_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.articles_id_seq', 1, false);


--
-- Name: comments_cid_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.comments_cid_seq', 1, false);


--
-- Name: feedbacks_id_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.feedbacks_id_seq', 1, false);


--
-- Name: user_status_access_levels_alid_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.user_status_access_levels_alid_seq', 1, false);


--
-- Name: user_status_access_levels_usid_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.user_status_access_levels_usid_seq', 1, false);


--
-- Name: users_statuses_usid_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.users_statuses_usid_seq', 1, false);


--
-- Name: users_uid_seq; Type: SEQUENCE SET; Schema: mublog; Owner: -
--

SELECT pg_catalog.setval('mublog.users_uid_seq', 1, false);


--
-- Name: access_levels access_levels_pkey; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.access_levels
    ADD CONSTRAINT access_levels_pkey PRIMARY KEY (alid);


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (aid);


--
-- Name: articles articles_unique_alias; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.articles
    ADD CONSTRAINT articles_unique_alias UNIQUE (alias);


--
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (cid);


--
-- Name: feedbacks feedbacks_pkey; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.feedbacks
    ADD CONSTRAINT feedbacks_pkey PRIMARY KEY (fbid);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (uid);


--
-- Name: users_statuses users_statuses_pkey; Type: CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.users_statuses
    ADD CONSTRAINT users_statuses_pkey PRIMARY KEY (usid);


--
-- Name: agent_hash; Type: INDEX; Schema: mublog; Owner: -
--

CREATE INDEX agent_hash ON mublog.users_sessions USING btree (agent_hash);


--
-- Name: article_comment; Type: INDEX; Schema: mublog; Owner: -
--

CREATE UNIQUE INDEX article_comment ON mublog.article_comments USING btree (aid, cid);


--
-- Name: mail; Type: INDEX; Schema: mublog; Owner: -
--

CREATE UNIQUE INDEX mail ON mublog.users USING btree (mail);


--
-- Name: nickname; Type: INDEX; Schema: mublog; Owner: -
--

CREATE UNIQUE INDEX nickname ON mublog.users USING btree (nickname);


--
-- Name: token; Type: INDEX; Schema: mublog; Owner: -
--

CREATE UNIQUE INDEX token ON mublog.users_sessions USING btree (token);


--
-- Name: uid; Type: INDEX; Schema: mublog; Owner: -
--

CREATE INDEX uid ON mublog.users_sessions USING btree (uid);


--
-- Name: usid_alid; Type: INDEX; Schema: mublog; Owner: -
--

CREATE UNIQUE INDEX usid_alid ON mublog.user_status_access_levels USING btree (usid, alid);


--
-- Name: article_comments fk_article_id; Type: FK CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.article_comments
    ADD CONSTRAINT fk_article_id FOREIGN KEY (aid) REFERENCES mublog.articles(aid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: article_comments fk_comment_id; Type: FK CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.article_comments
    ADD CONSTRAINT fk_comment_id FOREIGN KEY (cid) REFERENCES mublog.comments(cid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: comments fk_parent_comment_id; Type: FK CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.comments
    ADD CONSTRAINT fk_parent_comment_id FOREIGN KEY (pid) REFERENCES mublog.comments(cid) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: user_status_access_levels fk_user_status_access_level_id; Type: FK CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.user_status_access_levels
    ADD CONSTRAINT fk_user_status_access_level_id FOREIGN KEY (alid) REFERENCES mublog.access_levels(alid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_status_access_levels fk_user_status_id; Type: FK CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.user_status_access_levels
    ADD CONSTRAINT fk_user_status_id FOREIGN KEY (usid) REFERENCES mublog.users_statuses(usid) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users fk_user_status_id; Type: FK CONSTRAINT; Schema: mublog; Owner: -
--

ALTER TABLE ONLY mublog.users
    ADD CONSTRAINT fk_user_status_id FOREIGN KEY (usid) REFERENCES mublog.users_statuses(usid) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--


--
-- PostgreSQL database dump
--

\restrict mjzrOZCOwQVs9HjuCdAUMAKL3k8X6RxIxPkAEY14QZAg0Z6nFgsMjR4W6PgHKMr

-- Dumped from database version 15.15
-- Dumped by pg_dump version 15.14 (Ubuntu 15.14-1.pgdg22.04+1)

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
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: articles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.articles (
    id bigint NOT NULL,
    sku character varying(50) NOT NULL,
    name character varying(200) NOT NULL,
    weight numeric(10,3) NOT NULL
);


ALTER TABLE public.articles OWNER TO postgres;

--
-- Name: articles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.articles ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.articles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: countries; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.countries (
    id bigint NOT NULL,
    code character(2) NOT NULL,
    name character varying(100) NOT NULL
);


ALTER TABLE public.countries OWNER TO postgres;

--
-- Name: countries_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.countries ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.countries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO postgres;

--
-- Name: order_addresses; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_addresses (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    type character varying(20) NOT NULL,
    country_id bigint,
    region character varying(100) DEFAULT NULL::character varying,
    city character varying(200) DEFAULT NULL::character varying,
    address character varying(300) DEFAULT NULL::character varying,
    building character varying(200) DEFAULT NULL::character varying,
    apartment character varying(30) DEFAULT NULL::character varying,
    postal_code character varying(20) DEFAULT NULL::character varying,
    phone character varying(30) DEFAULT NULL::character varying,
    contact_name character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.order_addresses OWNER TO postgres;

--
-- Name: order_addresses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.order_addresses ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.order_addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: order_carrier; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_carrier (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    name character varying(100) DEFAULT NULL::character varying,
    contact_data character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.order_carrier OWNER TO postgres;

--
-- Name: order_carrier_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.order_carrier ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.order_carrier_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: order_delivery; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_delivery (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    type smallint DEFAULT 0 NOT NULL,
    price numeric(12,2) DEFAULT NULL::numeric,
    price_eur numeric(12,2) DEFAULT NULL::numeric,
    calculate_type smallint DEFAULT 0 NOT NULL,
    date_min date,
    date_max date,
    confirmed_min date,
    confirmed_max date,
    fast_pay_min date,
    fast_pay_max date,
    old_min date,
    old_max date,
    warehouse_data json,
    tracking_number character varying(50) DEFAULT NULL::character varying,
    fact_date timestamp without time zone
);


ALTER TABLE public.order_delivery OWNER TO postgres;

--
-- Name: order_delivery_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.order_delivery ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.order_delivery_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: order_items; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_items (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    article_id bigint NOT NULL,
    quantity numeric(12,3) NOT NULL,
    unit_price numeric(12,2) NOT NULL,
    unit_price_eur numeric(12,2) DEFAULT NULL::numeric,
    currency character(3) DEFAULT NULL::bpchar,
    measure character(2) DEFAULT NULL::bpchar,
    weight numeric(10,3) NOT NULL,
    packaging_count numeric(10,3) DEFAULT NULL::numeric,
    pallet_qty numeric(10,3) DEFAULT NULL::numeric,
    packaging_qty numeric(10,3) DEFAULT NULL::numeric,
    swimming_pool boolean DEFAULT false NOT NULL
);


ALTER TABLE public.order_items OWNER TO postgres;

--
-- Name: order_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.order_items ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.order_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: order_payment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.order_payment (
    id bigint NOT NULL,
    order_id bigint NOT NULL,
    pay_type smallint NOT NULL,
    vat_type smallint DEFAULT 0 NOT NULL,
    vat_number character varying(100) DEFAULT NULL::character varying,
    tax_number character varying(50) DEFAULT NULL::character varying,
    full_payment_date date,
    bank_transfer_requested boolean DEFAULT false,
    bank_details text,
    cur_rate numeric(10,4) DEFAULT 1,
    payment_euro boolean DEFAULT false
);


ALTER TABLE public.order_payment OWNER TO postgres;

--
-- Name: order_payment_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.order_payment ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.order_payment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id bigint NOT NULL,
    hash character(32) NOT NULL,
    number character varying(20) DEFAULT NULL::character varying,
    user_id bigint,
    manager_id bigint,
    status smallint DEFAULT 1 NOT NULL,
    locale character(5) NOT NULL,
    currency character(3) DEFAULT 'EUR'::bpchar NOT NULL,
    measure character(3) DEFAULT 'm'::bpchar NOT NULL,
    discount_percent smallint,
    name character varying(200) NOT NULL,
    description text,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.orders ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    email character varying(180) NOT NULL,
    name character varying(100) NOT NULL,
    role character varying(50) NOT NULL,
    created_at timestamp without time zone NOT NULL
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

ALTER TABLE public.users ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Data for Name: articles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.articles (id, sku, name, weight) FROM stdin;
5	SKU-001	Ceramic Tile White	18.500
6	SKU-002	Ceramic Tile Gray	19.200
\.


--
-- Data for Name: countries; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.countries (id, code, name) FROM stdin;
7	DE	Germany
8	PL	Poland
9	FR	France
\.


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20260119061632	2026-01-20 07:11:15	33
DoctrineMigrations\\Version20260119061715	2026-01-20 07:11:15	25
DoctrineMigrations\\Version20260119061843	2026-01-20 07:11:15	20
DoctrineMigrations\\Version20260119062036	2026-01-20 07:11:15	94
DoctrineMigrations\\Version20260119062350	2026-01-20 07:11:15	36
DoctrineMigrations\\Version20260119062510	2026-01-20 07:11:15	53
DoctrineMigrations\\Version20260119062559	2026-01-20 07:11:15	33
DoctrineMigrations\\Version20260119062630	2026-01-20 07:11:15	29
DoctrineMigrations\\Version20260119062804	2026-01-20 07:11:15	21
\.


--
-- Data for Name: order_addresses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_addresses (id, order_id, type, country_id, region, city, address, building, apartment, postal_code, phone, contact_name) FROM stdin;
1	1	delivery	7	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
2	2	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
3	3	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
4	4	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
5	5	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
6	6	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
7	7	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
8	8	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
9	9	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
10	10	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
11	11	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
12	12	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
13	13	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
14	14	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
15	15	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
16	16	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
17	17	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
18	18	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
19	19	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
20	20	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
21	21	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
22	22	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
23	23	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
24	24	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
25	25	delivery	\N	\N	Berlin	Alexanderplatz 1	\N	\N	\N	\N	\N
\.


--
-- Data for Name: order_carrier; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_carrier (id, order_id, name, contact_data) FROM stdin;
1	1	DHL	support@dhl.com
2	2	DHL	support@dhl.com
3	3	DHL	support@dhl.com
4	4	DHL	support@dhl.com
5	5	DHL	support@dhl.com
6	6	DHL	support@dhl.com
7	7	DHL	support@dhl.com
8	8	DHL	support@dhl.com
9	9	DHL	support@dhl.com
10	10	DHL	support@dhl.com
11	11	DHL	support@dhl.com
12	12	DHL	support@dhl.com
13	13	DHL	support@dhl.com
14	14	DHL	support@dhl.com
15	15	DHL	support@dhl.com
16	16	DHL	support@dhl.com
17	17	DHL	support@dhl.com
18	18	DHL	support@dhl.com
19	19	DHL	support@dhl.com
20	20	DHL	support@dhl.com
21	21	DHL	support@dhl.com
22	22	DHL	support@dhl.com
23	23	DHL	support@dhl.com
24	24	DHL	support@dhl.com
25	25	DHL	support@dhl.com
\.


--
-- Data for Name: order_delivery; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_delivery (id, order_id, type, price, price_eur, calculate_type, date_min, date_max, confirmed_min, confirmed_max, fast_pay_min, fast_pay_max, old_min, old_max, warehouse_data, tracking_number, fact_date) FROM stdin;
1	1	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
2	2	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
3	3	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
4	4	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
5	5	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
6	6	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
7	7	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
8	8	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
9	9	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
10	10	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
11	11	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
12	12	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
13	13	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
14	14	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
15	15	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
16	16	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
17	17	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
18	18	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
19	19	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
20	20	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
21	21	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
22	22	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
23	23	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
24	24	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
25	25	0	120.00	\N	0	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_items (id, order_id, article_id, quantity, unit_price, unit_price_eur, currency, measure, weight, packaging_count, pallet_qty, packaging_qty, swimming_pool) FROM stdin;
1	1	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
2	1	6	10.000	14.20	\N	\N	\N	19.200	\N	\N	\N	f
3	2	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
4	3	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
5	4	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
6	5	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
7	6	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
8	7	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
9	8	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
10	9	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
11	10	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
12	11	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
13	12	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
14	13	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
15	14	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
16	15	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
17	16	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
18	17	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
19	18	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
20	19	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
21	20	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
22	21	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
23	22	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
24	23	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
25	24	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
26	25	5	25.000	12.50	\N	\N	\N	18.500	\N	\N	\N	f
\.


--
-- Data for Name: order_payment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.order_payment (id, order_id, pay_type, vat_type, vat_number, tax_number, full_payment_date, bank_transfer_requested, bank_details, cur_rate, payment_euro) FROM stdin;
1	1	1	0	\N	\N	\N	f	\N	1.0000	f
2	2	1	0	\N	\N	\N	f	\N	1.0000	f
3	3	1	0	\N	\N	\N	f	\N	1.0000	f
4	4	1	0	\N	\N	\N	f	\N	1.0000	f
5	5	1	0	\N	\N	\N	f	\N	1.0000	f
6	6	1	0	\N	\N	\N	f	\N	1.0000	f
7	7	1	0	\N	\N	\N	f	\N	1.0000	f
8	8	1	0	\N	\N	\N	f	\N	1.0000	f
9	9	1	0	\N	\N	\N	f	\N	1.0000	f
10	10	1	0	\N	\N	\N	f	\N	1.0000	f
11	11	1	0	\N	\N	\N	f	\N	1.0000	f
12	12	1	0	\N	\N	\N	f	\N	1.0000	f
13	13	1	0	\N	\N	\N	f	\N	1.0000	f
14	14	1	0	\N	\N	\N	f	\N	1.0000	f
15	15	1	0	\N	\N	\N	f	\N	1.0000	f
16	16	1	0	\N	\N	\N	f	\N	1.0000	f
17	17	1	0	\N	\N	\N	f	\N	1.0000	f
18	18	1	0	\N	\N	\N	f	\N	1.0000	f
19	19	1	0	\N	\N	\N	f	\N	1.0000	f
20	20	1	0	\N	\N	\N	f	\N	1.0000	f
21	21	1	0	\N	\N	\N	f	\N	1.0000	f
22	22	1	0	\N	\N	\N	f	\N	1.0000	f
23	23	1	0	\N	\N	\N	f	\N	1.0000	f
24	24	1	0	\N	\N	\N	f	\N	1.0000	f
25	25	1	0	\N	\N	\N	f	\N	1.0000	f
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id, hash, number, user_id, manager_id, status, locale, currency, measure, discount_percent, name, description, created_at, updated_at) FROM stdin;
1	80fd9def8d780517b725beed38827843	ORD-0001	\N	6	1	de   	EUR	m  	\N	Test order	\N	2026-01-20 07:13:30	\N
2	35630f13dcbb0f8810e4991a8e94074b	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 07:15:52	\N
3	c4a654bc45d0431f24ff18d7ee19fc42	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:19:46	\N
4	35429d1582a4d491b6aa157671f507c4	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:20:44	\N
5	7edb353ff7bac3fb29c87ec03f3f7696	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:20:56	\N
6	f16dc9a909279a8ed7525d4423d13862	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:21:37	\N
7	30c06bdcc2e30bd3281b143cfdd47980	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:22:59	\N
8	09920a99a578720b71dd8b8a1557a67d	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:23:09	\N
9	fba9d0de664e9ddb164556698b196f3d	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:23:18	\N
10	2c2c9d1526a3e0c294161c07033f87b3	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:24:59	\N
11	e80857096770788d6cd6c6bcc8f72ea2	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:25:25	\N
12	3ed12c5584953a2e995d977395a29f1c	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:31:46	\N
13	8b995f8f8e583bcf7b9a9650faaa23f2	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:32:16	\N
14	f189458cc48ea64cceec7e4a772e4b0a	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:33:09	\N
15	5cac2706f01bfaf79b157d2a61a97968	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:33:11	\N
16	2b3843bdec6aefb4b6bf2515500b24f6	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:38:48	\N
17	688e064a35bfbc1d107f25b1de482b5a	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:38:49	\N
18	b6c8cdcae4d5d26f9138982f3d1381ac	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:39:00	\N
19	213b3728345529176c5cb72918f1e0b1	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:39:38	\N
20	93420ca20f19d58aa85c0666efa7ae69	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:39:54	\N
21	7f9cfb27e960e7402044dcc6bb3cba36	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:39:56	\N
22	565373fedc8ac7df45a31c9ca3ae5657	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:45:45	\N
23	d38a254be4b66df3e98de6e07e145abf	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:57:31	\N
24	4ca4346657c79723c43a656b2944ab8d	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 10:57:35	\N
25	9f666931aeb2bcad307f8b5e76f20952	\N	\N	\N	1	de   	EUR	m  	5	Order from API	\N	2026-01-20 11:50:28	\N
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id, email, name, role, created_at) FROM stdin;
5	client@test.local	Test Client	ROLE_USER	2026-01-20 07:13:30
6	manager@test.local	Order Manager	ROLE_MANAGER	2026-01-20 07:13:30
\.


--
-- Name: articles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.articles_id_seq', 6, true);


--
-- Name: countries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.countries_id_seq', 9, true);


--
-- Name: order_addresses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_addresses_id_seq', 25, true);


--
-- Name: order_carrier_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_carrier_id_seq', 25, true);


--
-- Name: order_delivery_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_delivery_id_seq', 25, true);


--
-- Name: order_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_items_id_seq', 26, true);


--
-- Name: order_payment_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.order_payment_id_seq', 25, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_seq', 25, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_seq', 6, true);


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: countries countries_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (id);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: order_addresses order_addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_addresses
    ADD CONSTRAINT order_addresses_pkey PRIMARY KEY (id);


--
-- Name: order_carrier order_carrier_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_carrier
    ADD CONSTRAINT order_carrier_pkey PRIMARY KEY (id);


--
-- Name: order_delivery order_delivery_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_delivery
    ADD CONSTRAINT order_delivery_pkey PRIMARY KEY (id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: order_payment order_payment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_payment
    ADD CONSTRAINT order_payment_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: idx_addr_country; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_addr_country ON public.order_addresses USING btree (country_id);


--
-- Name: idx_addr_order; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_addr_order ON public.order_addresses USING btree (order_id);


--
-- Name: idx_items_article; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_items_article ON public.order_items USING btree (article_id);


--
-- Name: idx_items_order; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_items_order ON public.order_items USING btree (order_id);


--
-- Name: idx_orders_created; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_created ON public.orders USING btree (created_at);


--
-- Name: idx_orders_manager; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_manager ON public.orders USING btree (manager_id);


--
-- Name: idx_orders_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_status ON public.orders USING btree (status);


--
-- Name: idx_orders_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_orders_user ON public.orders USING btree (user_id);


--
-- Name: uniq_articles_sku; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uniq_articles_sku ON public.articles USING btree (sku);


--
-- Name: uniq_countries_code; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uniq_countries_code ON public.countries USING btree (code);


--
-- Name: uniq_orders_hash; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uniq_orders_hash ON public.orders USING btree (hash);


--
-- Name: uniq_users_email; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX uniq_users_email ON public.users USING btree (email);


--
-- Name: order_addresses fk_addr_country; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_addresses
    ADD CONSTRAINT fk_addr_country FOREIGN KEY (country_id) REFERENCES public.countries(id);


--
-- Name: order_addresses fk_addr_order; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_addresses
    ADD CONSTRAINT fk_addr_order FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_carrier fk_carrier_order; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_carrier
    ADD CONSTRAINT fk_carrier_order FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_delivery fk_delivery_order; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_delivery
    ADD CONSTRAINT fk_delivery_order FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items fk_items_article; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT fk_items_article FOREIGN KEY (article_id) REFERENCES public.articles(id);


--
-- Name: order_items fk_items_order; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: orders fk_orders_manager; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_orders_manager FOREIGN KEY (manager_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: orders fk_orders_user; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: order_payment fk_payment_order; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.order_payment
    ADD CONSTRAINT fk_payment_order FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict mjzrOZCOwQVs9HjuCdAUMAKL3k8X6RxIxPkAEY14QZAg0Z6nFgsMjR4W6PgHKMr


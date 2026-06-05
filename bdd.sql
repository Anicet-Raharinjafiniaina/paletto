--
-- PostgreSQL database dump
--

-- Dumped from database version 17.4 (Debian 17.4-1.pgdg120+2)
-- Dumped by pg_dump version 17.4

-- Started on 2026-06-05 08:50:37

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 5 (class 2615 OID 25394)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 3565 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS '';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 217 (class 1259 OID 25396)
-- Name: acces; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.acces (
    id bigint NOT NULL,
    profil_id integer NOT NULL,
    page_id integer[],
    page_accueil smallint DEFAULT 0,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone
);


ALTER TABLE public.acces OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 25402)
-- Name: acces_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.acces_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.acces_id_seq OWNER TO postgres;

--
-- TOC entry 3567 (class 0 OID 0)
-- Dependencies: 218
-- Name: acces_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.acces_id_seq OWNED BY public.acces.id;


--
-- TOC entry 219 (class 1259 OID 25403)
-- Name: action; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.action (
    id integer NOT NULL,
    libelle character varying
);


ALTER TABLE public.action OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 25408)
-- Name: allee; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.allee (
    id bigint NOT NULL,
    code character varying,
    entrepot_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.allee OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 25414)
-- Name: allee_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.allee_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.allee_id_seq OWNER TO postgres;

--
-- TOC entry 3568 (class 0 OID 0)
-- Dependencies: 221
-- Name: allee_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.allee_id_seq OWNED BY public.allee.id;


--
-- TOC entry 222 (class 1259 OID 25415)
-- Name: article; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.article (
    id bigint NOT NULL,
    palette_id bigint,
    client_code character varying,
    client_nom character varying,
    code character varying,
    nom character varying,
    dluo date,
    unite_pcb character varying,
    quantite double precision DEFAULT 0,
    lot character varying,
    palettisation character varying,
    unite_stockage character varying,
    observation character varying,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0,
    qr_code_text character varying,
    qr_code_image text,
    affectee_emplacement smallint DEFAULT 0,
    actif smallint DEFAULT 1,
    mouvement_type_id smallint
);


ALTER TABLE public.article OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 25424)
-- Name: article_hors_x3; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.article_hors_x3 (
    id bigint NOT NULL,
    code character varying,
    nom character varying,
    unite_pcb text,
    palettisation text,
    unite_stockage character varying,
    description character varying,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.article_hors_x3 OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 25430)
-- Name: article_hors_x3_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.article_hors_x3_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.article_hors_x3_id_seq OWNER TO postgres;

--
-- TOC entry 3569 (class 0 OID 0)
-- Dependencies: 224
-- Name: article_hors_x3_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.article_hors_x3_id_seq OWNED BY public.article_hors_x3.id;


--
-- TOC entry 225 (class 1259 OID 25431)
-- Name: article_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.article_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.article_id_seq OWNER TO postgres;

--
-- TOC entry 3570 (class 0 OID 0)
-- Dependencies: 225
-- Name: article_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.article_id_seq OWNED BY public.article.id;


--
-- TOC entry 226 (class 1259 OID 25432)
-- Name: cage; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cage (
    id bigint NOT NULL,
    code character varying,
    entrepot_id smallint,
    allee_id smallint,
    rangee_id smallint,
    niveau_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.cage OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 25438)
-- Name: cage_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cage_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cage_id_seq OWNER TO postgres;

--
-- TOC entry 3571 (class 0 OID 0)
-- Dependencies: 227
-- Name: cage_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cage_id_seq OWNED BY public.cage.id;


--
-- TOC entry 228 (class 1259 OID 25439)
-- Name: emplacement; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.emplacement (
    id bigint NOT NULL,
    code character varying,
    entrepot_id smallint,
    allee_id smallint,
    rangee_id smallint,
    niveau_id smallint,
    cage_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.emplacement OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 25445)
-- Name: emplacement_adresse; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.emplacement_adresse (
    id bigint NOT NULL,
    qr_code_texte character varying,
    qr_code_image text,
    emplacement_id smallint,
    emplacement_statut_id smallint DEFAULT 1,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.emplacement_adresse OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 25452)
-- Name: emplacement_statut; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.emplacement_statut (
    id smallint NOT NULL,
    statut character varying
);


ALTER TABLE public.emplacement_statut OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 25457)
-- Name: entrepot; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.entrepot (
    id bigint NOT NULL,
    code character varying,
    nom character varying,
    localisation character varying,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.entrepot OWNER TO postgres;

--
-- TOC entry 232 (class 1259 OID 25463)
-- Name: niveau; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.niveau (
    id bigint NOT NULL,
    code character varying,
    entrepot_id smallint,
    allee_id smallint,
    rangee_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.niveau OWNER TO postgres;

--
-- TOC entry 233 (class 1259 OID 25469)
-- Name: rangee; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.rangee (
    id bigint NOT NULL,
    code character varying,
    entrepot_id smallint,
    allee_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.rangee OWNER TO postgres;

--
-- TOC entry 256 (class 1259 OID 25713)
-- Name: emplacement_adresse_view; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.emplacement_adresse_view AS
 SELECT emplacement_adresse.id AS emplacement_adresse_id,
    entrepot.code AS entrepot_code,
    entrepot.id AS entrepot_id,
    entrepot.nom AS entrepot_nom,
    allee.code AS allee_code,
    allee.id AS allee_id,
    rangee.code AS rangee_code,
    rangee.id AS rangee_id,
    niveau.code AS niveau_code,
    niveau.id AS niveau_id,
    cage.code AS cage_code,
    cage.id AS cage_id,
    emplacement.code AS emplacement_code,
    emplacement.id AS emplacement_id,
    emplacement_statut.statut,
    emplacement_adresse.emplacement_statut_id AS statut_id,
    (((((((((((entrepot.code)::text || '-'::text) || (allee.code)::text) || '-'::text) || (rangee.code)::text) || '-'::text) || (niveau.code)::text) || '-'::text) || (cage.code)::text) || '-'::text) || (emplacement.code)::text) AS qr_code_texte,
    emplacement_adresse.qr_code_image,
    emplacement_adresse.date_creation,
    emplacement_adresse.date_modification
   FROM (((((((public.emplacement
     LEFT JOIN public.entrepot ON ((entrepot.id = emplacement.entrepot_id)))
     LEFT JOIN public.allee ON ((allee.id = emplacement.allee_id)))
     LEFT JOIN public.rangee ON ((rangee.id = emplacement.rangee_id)))
     LEFT JOIN public.niveau ON ((niveau.id = emplacement.niveau_id)))
     LEFT JOIN public.cage ON ((cage.id = emplacement.cage_id)))
     LEFT JOIN public.emplacement_adresse ON ((emplacement_adresse.emplacement_id = emplacement.id)))
     LEFT JOIN public.emplacement_statut ON ((emplacement_statut.id = emplacement_adresse.emplacement_statut_id)))
  WHERE ((emplacement.flag_suppression = 0) AND (cage.flag_suppression = 0) AND (niveau.flag_suppression = 0) AND (rangee.flag_suppression = 0) AND (allee.flag_suppression = 0) AND (entrepot.flag_suppression = 0));


ALTER VIEW public.emplacement_adresse_view OWNER TO postgres;

--
-- TOC entry 234 (class 1259 OID 25480)
-- Name: emplacement_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.emplacement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.emplacement_id_seq OWNER TO postgres;

--
-- TOC entry 3572 (class 0 OID 0)
-- Dependencies: 234
-- Name: emplacement_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.emplacement_id_seq OWNED BY public.emplacement_adresse.id;


--
-- TOC entry 235 (class 1259 OID 25481)
-- Name: emplacement_id_seq1; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.emplacement_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.emplacement_id_seq1 OWNER TO postgres;

--
-- TOC entry 3573 (class 0 OID 0)
-- Dependencies: 235
-- Name: emplacement_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.emplacement_id_seq1 OWNED BY public.emplacement.id;


--
-- TOC entry 236 (class 1259 OID 25482)
-- Name: entrepot_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.entrepot_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.entrepot_id_seq OWNER TO postgres;

--
-- TOC entry 3574 (class 0 OID 0)
-- Dependencies: 236
-- Name: entrepot_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.entrepot_id_seq OWNED BY public.entrepot.id;


--
-- TOC entry 237 (class 1259 OID 25483)
-- Name: historique; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.historique (
    id bigint NOT NULL,
    data_json text,
    utilisateur_id integer,
    date_creation timestamp without time zone DEFAULT now(),
    action_id integer
);


ALTER TABLE public.historique OWNER TO postgres;

--
-- TOC entry 238 (class 1259 OID 25489)
-- Name: historique_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.historique_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.historique_id_seq OWNER TO postgres;

--
-- TOC entry 3575 (class 0 OID 0)
-- Dependencies: 238
-- Name: historique_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.historique_id_seq OWNED BY public.historique.id;


--
-- TOC entry 239 (class 1259 OID 25490)
-- Name: mouvement; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mouvement (
    id bigint NOT NULL,
    mouvement_type_id smallint,
    emplacement_id smallint,
    palette_id smallint,
    article_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    actif smallint DEFAULT 1,
    flag_suppression smallint DEFAULT 0,
    date_mouvement timestamp without time zone
);


ALTER TABLE public.mouvement OWNER TO postgres;

--
-- TOC entry 240 (class 1259 OID 25495)
-- Name: mouvement_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mouvement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mouvement_id_seq OWNER TO postgres;

--
-- TOC entry 3576 (class 0 OID 0)
-- Dependencies: 240
-- Name: mouvement_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mouvement_id_seq OWNED BY public.mouvement.id;


--
-- TOC entry 241 (class 1259 OID 25496)
-- Name: mouvement_type; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mouvement_type (
    id bigint NOT NULL,
    type character varying
);


ALTER TABLE public.mouvement_type OWNER TO postgres;

--
-- TOC entry 242 (class 1259 OID 25501)
-- Name: mouvement_type_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mouvement_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mouvement_type_id_seq OWNER TO postgres;

--
-- TOC entry 3577 (class 0 OID 0)
-- Dependencies: 242
-- Name: mouvement_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mouvement_type_id_seq OWNED BY public.mouvement_type.id;


--
-- TOC entry 243 (class 1259 OID 25502)
-- Name: niveau_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.niveau_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.niveau_id_seq OWNER TO postgres;

--
-- TOC entry 3578 (class 0 OID 0)
-- Dependencies: 243
-- Name: niveau_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.niveau_id_seq OWNED BY public.niveau.id;


--
-- TOC entry 244 (class 1259 OID 25503)
-- Name: page; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.page (
    id bigint NOT NULL,
    libelle character varying,
    actif smallint DEFAULT 1,
    icone character varying,
    lien character varying,
    section_id integer,
    sous_section_id smallint,
    ordre smallint,
    show_menu smallint DEFAULT 1
);


ALTER TABLE public.page OWNER TO postgres;

--
-- TOC entry 245 (class 1259 OID 25510)
-- Name: page_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.page_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.page_id_seq OWNER TO postgres;

--
-- TOC entry 3579 (class 0 OID 0)
-- Dependencies: 245
-- Name: page_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.page_id_seq OWNED BY public.page.id;


--
-- TOC entry 246 (class 1259 OID 25511)
-- Name: palette; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.palette (
    id bigint NOT NULL,
    code character varying,
    client_code character varying,
    client_nom character varying,
    palette_statut_id smallint,
    cree_par smallint,
    date_creation timestamp without time zone,
    modifie_par smallint,
    date_modification timestamp without time zone,
    supprime_par smallint,
    date_suppression timestamp without time zone,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.palette OWNER TO postgres;

--
-- TOC entry 247 (class 1259 OID 25517)
-- Name: palette_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.palette_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.palette_id_seq OWNER TO postgres;

--
-- TOC entry 3580 (class 0 OID 0)
-- Dependencies: 247
-- Name: palette_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.palette_id_seq OWNED BY public.palette.id;


--
-- TOC entry 248 (class 1259 OID 25518)
-- Name: palette_statut; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.palette_statut (
    id smallint NOT NULL,
    statut character varying
);


ALTER TABLE public.palette_statut OWNER TO postgres;

--
-- TOC entry 249 (class 1259 OID 25523)
-- Name: profil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profil (
    id bigint NOT NULL,
    libelle character varying,
    actif smallint DEFAULT 1,
    date_creation timestamp without time zone,
    cree_par integer,
    date_modification timestamp without time zone,
    modifie_par integer,
    date_suppression timestamp without time zone,
    supprime_par integer,
    flag_suppression smallint DEFAULT 0
);


ALTER TABLE public.profil OWNER TO postgres;

--
-- TOC entry 250 (class 1259 OID 25530)
-- Name: profil_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.profil_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.profil_id_seq OWNER TO postgres;

--
-- TOC entry 3581 (class 0 OID 0)
-- Dependencies: 250
-- Name: profil_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.profil_id_seq OWNED BY public.profil.id;


--
-- TOC entry 251 (class 1259 OID 25531)
-- Name: rangee_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.rangee_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rangee_id_seq OWNER TO postgres;

--
-- TOC entry 3582 (class 0 OID 0)
-- Dependencies: 251
-- Name: rangee_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.rangee_id_seq OWNED BY public.rangee.id;


--
-- TOC entry 252 (class 1259 OID 25532)
-- Name: section; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.section (
    id bigint NOT NULL,
    libelle character varying,
    actif smallint DEFAULT 1,
    icone character varying,
    ordre smallint DEFAULT 0,
    avoir_sous_section smallint DEFAULT 0,
    image text
);


ALTER TABLE public.section OWNER TO postgres;

--
-- TOC entry 253 (class 1259 OID 25540)
-- Name: section_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.section_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.section_id_seq OWNER TO postgres;

--
-- TOC entry 3583 (class 0 OID 0)
-- Dependencies: 253
-- Name: section_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.section_id_seq OWNED BY public.section.id;


--
-- TOC entry 254 (class 1259 OID 25541)
-- Name: utilisateur; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.utilisateur (
    id bigint NOT NULL,
    login character varying,
    nom character varying,
    mail character varying,
    fonction character varying,
    profil_id smallint,
    cree_par smallint,
    modifie_par smallint,
    supprime_par smallint,
    flag_suppression smallint DEFAULT 0,
    actif smallint DEFAULT 1,
    date_creation timestamp without time zone,
    date_modification timestamp without time zone,
    date_suppression timestamp without time zone,
    derniere_connexion timestamp without time zone
);


ALTER TABLE public.utilisateur OWNER TO postgres;

--
-- TOC entry 255 (class 1259 OID 25548)
-- Name: utilisateur_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.utilisateur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.utilisateur_id_seq OWNER TO postgres;

--
-- TOC entry 3584 (class 0 OID 0)
-- Dependencies: 255
-- Name: utilisateur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.utilisateur_id_seq OWNED BY public.utilisateur.id;


--
-- TOC entry 3311 (class 2604 OID 25549)
-- Name: acces id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.acces ALTER COLUMN id SET DEFAULT nextval('public.acces_id_seq'::regclass);


--
-- TOC entry 3313 (class 2604 OID 25550)
-- Name: allee id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.allee ALTER COLUMN id SET DEFAULT nextval('public.allee_id_seq'::regclass);


--
-- TOC entry 3315 (class 2604 OID 25551)
-- Name: article id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.article ALTER COLUMN id SET DEFAULT nextval('public.article_id_seq'::regclass);


--
-- TOC entry 3320 (class 2604 OID 25552)
-- Name: article_hors_x3 id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.article_hors_x3 ALTER COLUMN id SET DEFAULT nextval('public.article_hors_x3_id_seq'::regclass);


--
-- TOC entry 3322 (class 2604 OID 25553)
-- Name: cage id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cage ALTER COLUMN id SET DEFAULT nextval('public.cage_id_seq'::regclass);


--
-- TOC entry 3324 (class 2604 OID 25554)
-- Name: emplacement id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement ALTER COLUMN id SET DEFAULT nextval('public.emplacement_id_seq1'::regclass);


--
-- TOC entry 3326 (class 2604 OID 25555)
-- Name: emplacement_adresse id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement_adresse ALTER COLUMN id SET DEFAULT nextval('public.emplacement_id_seq'::regclass);


--
-- TOC entry 3329 (class 2604 OID 25556)
-- Name: entrepot id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrepot ALTER COLUMN id SET DEFAULT nextval('public.entrepot_id_seq'::regclass);


--
-- TOC entry 3335 (class 2604 OID 25557)
-- Name: historique id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historique ALTER COLUMN id SET DEFAULT nextval('public.historique_id_seq'::regclass);


--
-- TOC entry 3337 (class 2604 OID 25558)
-- Name: mouvement id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement ALTER COLUMN id SET DEFAULT nextval('public.mouvement_id_seq'::regclass);


--
-- TOC entry 3340 (class 2604 OID 25559)
-- Name: mouvement_type id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement_type ALTER COLUMN id SET DEFAULT nextval('public.mouvement_type_id_seq'::regclass);


--
-- TOC entry 3331 (class 2604 OID 25560)
-- Name: niveau id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.niveau ALTER COLUMN id SET DEFAULT nextval('public.niveau_id_seq'::regclass);


--
-- TOC entry 3341 (class 2604 OID 25561)
-- Name: page id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.page ALTER COLUMN id SET DEFAULT nextval('public.page_id_seq'::regclass);


--
-- TOC entry 3344 (class 2604 OID 25562)
-- Name: palette id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.palette ALTER COLUMN id SET DEFAULT nextval('public.palette_id_seq'::regclass);


--
-- TOC entry 3346 (class 2604 OID 25563)
-- Name: profil id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profil ALTER COLUMN id SET DEFAULT nextval('public.profil_id_seq'::regclass);


--
-- TOC entry 3333 (class 2604 OID 25564)
-- Name: rangee id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rangee ALTER COLUMN id SET DEFAULT nextval('public.rangee_id_seq'::regclass);


--
-- TOC entry 3349 (class 2604 OID 25565)
-- Name: section id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section ALTER COLUMN id SET DEFAULT nextval('public.section_id_seq'::regclass);


--
-- TOC entry 3353 (class 2604 OID 25566)
-- Name: utilisateur id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateur ALTER COLUMN id SET DEFAULT nextval('public.utilisateur_id_seq'::regclass);


--
-- TOC entry 3357 (class 2606 OID 25576)
-- Name: acces acces_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.acces
    ADD CONSTRAINT acces_pkey PRIMARY KEY (id);


--
-- TOC entry 3359 (class 2606 OID 25578)
-- Name: action action_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.action
    ADD CONSTRAINT action_pkey PRIMARY KEY (id);


--
-- TOC entry 3361 (class 2606 OID 25580)
-- Name: allee allee_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.allee
    ADD CONSTRAINT allee_pkey PRIMARY KEY (id);


--
-- TOC entry 3365 (class 2606 OID 25582)
-- Name: article_hors_x3 article_hors_x3_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.article_hors_x3
    ADD CONSTRAINT article_hors_x3_pkey PRIMARY KEY (id);


--
-- TOC entry 3363 (class 2606 OID 25584)
-- Name: article article_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.article
    ADD CONSTRAINT article_pkey PRIMARY KEY (id);


--
-- TOC entry 3367 (class 2606 OID 25586)
-- Name: cage cage_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cage
    ADD CONSTRAINT cage_pkey PRIMARY KEY (id);


--
-- TOC entry 3369 (class 2606 OID 25588)
-- Name: emplacement emplacement_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement
    ADD CONSTRAINT emplacement_pkey PRIMARY KEY (id);


--
-- TOC entry 3373 (class 2606 OID 25590)
-- Name: emplacement_statut emplacement_statut_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement_statut
    ADD CONSTRAINT emplacement_statut_pkey PRIMARY KEY (id);


--
-- TOC entry 3375 (class 2606 OID 25592)
-- Name: entrepot entrepot_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.entrepot
    ADD CONSTRAINT entrepot_pkey PRIMARY KEY (id);


--
-- TOC entry 3381 (class 2606 OID 25594)
-- Name: historique historique_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historique
    ADD CONSTRAINT historique_pkey PRIMARY KEY (id);


--
-- TOC entry 3371 (class 2606 OID 25596)
-- Name: emplacement_adresse liste_emplacement_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement_adresse
    ADD CONSTRAINT liste_emplacement_pkey PRIMARY KEY (id);


--
-- TOC entry 3383 (class 2606 OID 25598)
-- Name: mouvement mouvement_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement
    ADD CONSTRAINT mouvement_pkey PRIMARY KEY (id);


--
-- TOC entry 3385 (class 2606 OID 25600)
-- Name: mouvement_type mouvement_type_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement_type
    ADD CONSTRAINT mouvement_type_pkey PRIMARY KEY (id);


--
-- TOC entry 3377 (class 2606 OID 25602)
-- Name: niveau niveau_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.niveau
    ADD CONSTRAINT niveau_pkey PRIMARY KEY (id);


--
-- TOC entry 3387 (class 2606 OID 25604)
-- Name: page page_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.page
    ADD CONSTRAINT page_pkey PRIMARY KEY (id);


--
-- TOC entry 3389 (class 2606 OID 25606)
-- Name: palette palette_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.palette
    ADD CONSTRAINT palette_pkey PRIMARY KEY (id);


--
-- TOC entry 3391 (class 2606 OID 25608)
-- Name: palette_statut pallette_statut_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.palette_statut
    ADD CONSTRAINT pallette_statut_pkey PRIMARY KEY (id);


--
-- TOC entry 3393 (class 2606 OID 25610)
-- Name: profil profil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profil
    ADD CONSTRAINT profil_pkey PRIMARY KEY (id);


--
-- TOC entry 3379 (class 2606 OID 25612)
-- Name: rangee rangee_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rangee
    ADD CONSTRAINT rangee_pkey PRIMARY KEY (id);


--
-- TOC entry 3395 (class 2606 OID 25614)
-- Name: section section_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.section
    ADD CONSTRAINT section_pkey PRIMARY KEY (id);


--
-- TOC entry 3397 (class 2606 OID 25616)
-- Name: utilisateur utilisateur_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateur
    ADD CONSTRAINT utilisateur_pkey PRIMARY KEY (id);


--
-- TOC entry 3399 (class 2606 OID 25643)
-- Name: allee fk_allee_entrepot; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.allee
    ADD CONSTRAINT fk_allee_entrepot FOREIGN KEY (entrepot_id) REFERENCES public.entrepot(id);


--
-- TOC entry 3400 (class 2606 OID 25688)
-- Name: article fk_article_palette; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.article
    ADD CONSTRAINT fk_article_palette FOREIGN KEY (palette_id) REFERENCES public.palette(id);


--
-- TOC entry 3401 (class 2606 OID 25628)
-- Name: cage fk_cage_niveau; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cage
    ADD CONSTRAINT fk_cage_niveau FOREIGN KEY (niveau_id) REFERENCES public.niveau(id);


--
-- TOC entry 3403 (class 2606 OID 25648)
-- Name: emplacement_adresse fk_emplacement_adresse_emplacement; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement_adresse
    ADD CONSTRAINT fk_emplacement_adresse_emplacement FOREIGN KEY (emplacement_id) REFERENCES public.emplacement(id);


--
-- TOC entry 3404 (class 2606 OID 25653)
-- Name: emplacement_adresse fk_emplacement_adresse_emplacement_statut; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement_adresse
    ADD CONSTRAINT fk_emplacement_adresse_emplacement_statut FOREIGN KEY (emplacement_statut_id) REFERENCES public.emplacement_statut(id);


--
-- TOC entry 3402 (class 2606 OID 25623)
-- Name: emplacement fk_emplacement_cage; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.emplacement
    ADD CONSTRAINT fk_emplacement_cage FOREIGN KEY (cage_id) REFERENCES public.cage(id);


--
-- TOC entry 3407 (class 2606 OID 25703)
-- Name: historique fk_historique_action; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.historique
    ADD CONSTRAINT fk_historique_action FOREIGN KEY (action_id) REFERENCES public.action(id);


--
-- TOC entry 3408 (class 2606 OID 25678)
-- Name: mouvement fk_mouvement_emplacement_adresse; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement
    ADD CONSTRAINT fk_mouvement_emplacement_adresse FOREIGN KEY (emplacement_id) REFERENCES public.emplacement_adresse(id);


--
-- TOC entry 3409 (class 2606 OID 25698)
-- Name: mouvement fk_mouvement_mouvement_type; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement
    ADD CONSTRAINT fk_mouvement_mouvement_type FOREIGN KEY (mouvement_type_id) REFERENCES public.mouvement_type(id);


--
-- TOC entry 3410 (class 2606 OID 25683)
-- Name: mouvement fk_mouvement_palette; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mouvement
    ADD CONSTRAINT fk_mouvement_palette FOREIGN KEY (palette_id) REFERENCES public.palette(id);


--
-- TOC entry 3405 (class 2606 OID 25633)
-- Name: niveau fk_niveau_rangee; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.niveau
    ADD CONSTRAINT fk_niveau_rangee FOREIGN KEY (rangee_id) REFERENCES public.rangee(id);


--
-- TOC entry 3411 (class 2606 OID 25693)
-- Name: page fk_page_section; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.page
    ADD CONSTRAINT fk_page_section FOREIGN KEY (section_id) REFERENCES public.section(id);


--
-- TOC entry 3412 (class 2606 OID 25673)
-- Name: palette fk_palette_palette_statut; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.palette
    ADD CONSTRAINT fk_palette_palette_statut FOREIGN KEY (palette_statut_id) REFERENCES public.palette_statut(id);


--
-- TOC entry 3398 (class 2606 OID 25668)
-- Name: acces fk_profil_acces; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.acces
    ADD CONSTRAINT fk_profil_acces FOREIGN KEY (profil_id) REFERENCES public.profil(id);


--
-- TOC entry 3406 (class 2606 OID 25638)
-- Name: rangee fk_rangee_allee; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.rangee
    ADD CONSTRAINT fk_rangee_allee FOREIGN KEY (allee_id) REFERENCES public.allee(id);


--
-- TOC entry 3413 (class 2606 OID 25658)
-- Name: utilisateur fk_utilisateur_profil; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.utilisateur
    ADD CONSTRAINT fk_utilisateur_profil FOREIGN KEY (profil_id) REFERENCES public.profil(id);


--
-- TOC entry 3566 (class 0 OID 0)
-- Dependencies: 5
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;


-- Completed on 2026-06-05 08:50:45

--
-- PostgreSQL database dump complete
--


--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- Data for Name: accounts; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY accounts (account_id, account_identity, phash, active_email, type, operational, created_date, last_login, last_login_failure, karma_total, last_ip, confirmed, verification_number, oauth_provider, oauth_id) FROM stdin;
2948	beagle@example.com	$2a$10$u29BLJG5sQd/XbXX.RFJ4urOnbykByZUTc2ugUzalKWMPpH7QkZfq	beagle@example.com	0	t	2010-02-18 14:20:17.319739	2013-05-13 20:01:42.763911	2012-04-01 17:11:03.366223	8	173.203.99.229	1	419826	\N	\N
5442	tchalvak@gmail.com	$2a$10$flGAKaD0M9y.P6Z.eqaSDOycEiGROR8j7uMzZbRBR7UOiOlKLGidG	tchalvak@gmail.com	0	t	2009-11-03 12:04:53.223615	2015-08-05 22:31:56.826541	2015-07-30 07:53:32.800106	7	127.0.0.1	1	2345234	\N	\N
\.


--
-- Data for Name: news; Type: TABLE DATA; Schema: public; Owner: kzqai
--

COPY news (news_id, title, content, created, updated, tags) FROM stdin;
\.


--
-- Data for Name: account_news; Type: TABLE DATA; Schema: public; Owner: kzqai
--

COPY account_news (_account_id, _news_id, created_date) FROM stdin;
\.


--
-- Data for Name: class; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY class (class_id, class_name, class_active, class_note, class_tier, class_desc, class_icon, theme, identity) FROM stdin;
1	Viper	t	Poison	1	\N	\N	Black	viper
2	Crane	t	Speed	1	\N	\N	Blue	crane
3	Tiger	t	Strength	1	\N	\N	Red	tiger
4	Dragon	t	Healing	1	\N	\N	White	dragon
\.


--
-- Data for Name: players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY players (player_id, uname, health, strength, gold, messages, kills, turns, verification_number, active, level, status, member, days, bounty, created_date, resurrection_time, last_started_attack, energy, avatar_type, _class_id, ki, stamina, speed, karma, kills_gained, kills_used, description, instincts, beliefs, goals, traits) FROM stdin;
134853	Beagle	0	40	0	Kill me, Im an admin.	82	101	9897	1	8	0	0	16	0	2010-02-18 14:20:17.319739	18	2013-01-31 20:05:48.999044	1000	1	3	3	40	40	8	175	140					
128274	Tchalvak	0	90	4678	Contact me via the staff page, or use the official email, ninjawarslivebythesword@gmail.com\r\n\r\nDang\r\nBat\r\nCrazy\r\nDevil	394	63	3259	1	18	0	0	0	0	2009-11-03 12:04:53.223615	12	2015-08-05 18:31:20.799019	1000	1	1	803	90	90	29	126	105					
\.


--
-- Data for Name: account_players; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY account_players (_account_id, _player_id, last_login, created_date) FROM stdin;
5442	128274	2010-08-02 22:38:39.874268	2009-11-03 12:04:53.223615
2948	134853	2010-08-02 22:38:39.874268	2010-02-18 14:20:17.319739
\.


--
-- Name: accounts_account_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('accounts_account_id_seq', 28065, true);


--
-- Data for Name: chat; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY chat (chat_id, sender_id, message, date) FROM stdin;
\.


--
-- Data for Name: clan; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY clan (clan_id, clan_name, clan_created_date, clan_founder, clan_avatar_url, description) FROM stdin;
1	clan_fixture_test1	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
112	clan_fixture_test112	2010-09-24 23:21:16.400153	Tchalvak	\N	fixtures_test
108	clan_fixture_test108	2010-09-13 06:42:09.402398	Tchalvak	\N	fixtures_test
77	clan_fixture_test77	2010-08-11 01:12:05.260692	Tchalvak		fixtures_test
131	clan_fixture_test131	2010-10-16 00:55:59.411072	Tchalvak	\N	fixtures_test
94	clan_fixture_test94	2010-08-19 10:26:26.545643	Tchalvak	\N	fixtures_test
193	clan_fixture_test193	2011-10-03 21:48:44.429139	Tchalvak	\N	fixtures_test
168	clan_fixture_test168	2011-04-11 14:17:25.810824	Tchalvak	\N	fixtures_test
75	clan_fixture_test75	2010-08-10 00:37:38.655621	Tchalvak	\N	fixtures_test
133	clan_fixture_test133	2010-11-07 21:25:33.436599	Tchalvak	\N	fixtures_test
135	clan_fixture_test135	2010-11-25 18:53:05.393573	Tchalvak	\N	fixtures_test
102	clan_fixture_test102	2010-09-11 10:26:28.840892	Tchalvak		fixtures_test
104	clan_fixture_test104	2010-09-11 16:59:30.916074	Tchalvak	\N	fixtures_test
117	clan_fixture_test117	2010-09-30 03:24:19.155782	Tchalvak	\N	fixtures_test
20	clan_fixture_test20	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
27	clan_fixture_test27	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
29	clan_fixture_test29	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
32	clan_fixture_test32	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
36	clan_fixture_test36	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
139	clan_fixture_test139	2010-12-14 20:47:01.08422	Tchalvak	\N	fixtures_test
39	clan_fixture_test39	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
40	clan_fixture_test40	2010-03-25 17:10:09.010102	Tchalvak	\N	fixtures_test
169	clan_fixture_test169	2011-04-19 18:18:59.416078	Tchalvak	http://img847.imageshack.us/img847/9133/830pxvariaflag.png	fixtures_test
47	clan_fixture_test47	2010-05-16 05:45:08.614659	Tchalvak	\N	fixtures_test
45	clan_fixture_test45	2010-04-29 09:10:54.064977	Tchalvak	\N	fixtures_test
232	clan_fixture_test232	2013-06-04 06:39:09.399985	Tchalvak		fixtures_test
228	clan_fixture_test228	2013-03-09 16:21:00.62408	Tchalvak	\N	fixtures_test
195	clan_fixture_test195	2011-10-30 10:43:52.477042	Tchalvak		fixtures_test
237	clan_fixture_test237	2014-09-13 01:07:33.002409	Tchalvak	\N	fixtures_test
245	clan_fixture_test245	2015-08-03 21:14:45.596097	Tchalvak		fixtures_test
238	clan_fixture_test238	2015-02-21 15:40:23.872798	Tchalvak	\N	fixtures_test
213	clan_fixture_test213	2012-06-14 18:27:24.796132	Tchalvak		fixtures_test
207	clan_fixture_test207	2012-03-23 21:49:53.801076	Tchalvak		fixtures_test
243	clan_fixture_test243	2015-07-17 17:41:51.927762	Tchalvak	https://imagizer.imageshack.us/v2/720x302q90/540/LIRqKN.jpg	fixtures_test
230	clan_fixture_test230	2013-04-29 16:15:16.04129	Tchalvak	\N	fixtures_test
183	clan_fixture_test183	2011-08-19 19:42:59.50721	Tchalvak	\N	fixtures_test
220	clan_fixture_test220	2012-09-02 01:34:11.053294	Tchalvak	\N	fixtures_test
41	clan_fixture_test41	2010-03-25 17:10:09.010102	Tchalvak		fixtures_test
190	clan_fixture_test190	2011-09-25 03:33:43.047736	Tchalvak	\N	fixtures_test
223	clan_fixture_test223	2012-10-04 13:24:50.19241	Tchalvak	\N	fixtures_test
224	clan_fixture_test224	2012-10-07 12:49:21.33491	Tchalvak	\N	fixtures_test
\.


--
-- Name: clan_clan_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('clan_clan_id_seq', 245, true);


--
-- Data for Name: clan_player; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY clan_player (_clan_id, _player_id, member_level) FROM stdin;
\.


--
-- Name: class_class_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('class_class_id_seq', 6, true);


--
-- Data for Name: skill; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY skill (skill_id, skill_level, skill_is_active, skill_display_name, skill_internal_name, skill_type) FROM stdin;
1	1	t	Ice Bolt	ice	targeted
2	6	t	Cold Steal	coldsteal	targeted
3	1	t	Speed	speed	passive
4	1	t	Chi	chi	passive
6	1	t	Fire Bolt	fire	targeted
7	1	t	Blaze	blaze	combat
8	2	t	Deflect	deflect	combat
9	1	t	Poison Touch	poison	targeted
10	1	t	Hidden Resurrect	stealthres	passive
11	1	t	Sight	sight	targeted
12	1	t	Stealth	stealth	self-only
13	1	t	Unstealth	unstealth	self-only
14	2	t	Steal	steal	targeted
15	2	t	Kampo	kampo	self-only
16	2	t	Evasion	evasion	combat
5	20	t	Midnight Heal	midnightheal	passive
17	1	t	Heal	heal	targeted
\.


--
-- Data for Name: class_skill; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY class_skill (_class_id, _skill_id, class_skill_level) FROM stdin;
1	9	\N
1	10	\N
2	1	\N
2	3	\N
4	4	\N
3	6	\N
3	7	\N
4	17	\N
2	15	\N
4	16	\N
\.


--
-- Data for Name: dueling_log; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY dueling_log (id, attacker, defender, won, killpoints, date) FROM stdin;
\.


--
-- Name: dueling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('dueling_log_id_seq', 1281378, true);


--
-- Data for Name: effects; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY effects (effect_id, effect_identity, effect_name, effect_verb, effect_self) FROM stdin;
1	wound	Wound	Wounds	f
2	fire	Fire	Burns	f
3	ice	Ice	Freezes	f
4	shock	Shock	Shocks	f
5	acid	Acid	Dissolves	f
6	void	Void	Taints	f
7	flare	Flare	Blinds	f
8	poison	Poison	Poisons	f
9	paralysis	Paralysis	Paralyzes	f
10	slice	Slice	Slices	f
11	bash	Bash	Bashes	f
12	pierce	Pierce	Pierces	f
13	slow	Slow	Slows down	f
14	speed	Speed	Speeds up	t
15	stealth	Stealthed	Hides	t
16	vigor	Vigor	Energizes	t
17	strength	Strength	Strengthens	t
18	weaken	Weaken	Weakens	f
19	heal	Heal	Heals	t
20	healing	Healing	Healed	t
21	regen	Regenerate	Regenerating	t
22	death	Death	Dying	f
\.


--
-- Name: effects_effect_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('effects_effect_id_seq', 22, true);


--
-- Data for Name: enemies; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY enemies (_player_id, _enemy_id) FROM stdin;
\.


--
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY events (event_id, send_to, send_from, message, unread, date) FROM stdin;
\.


--
-- Name: events_event_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('events_event_id_seq', 7329639, true);


--
-- Data for Name: flags; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY flags (flag_id, flag, flag_type) FROM stdin;
1	bugabuse	2
2	multiplaying	3
3	spamming	4
4	paused	10
5	moderator	21
6	bugfinder	22
\.


--
-- Name: flags_flag_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('flags_flag_id_seq', 1, false);


--
-- Data for Name: item; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY item (item_id, item_internal_name, item_display_name, item_cost, image, for_sale, usage, ignore_stealth, covert, turn_cost, target_damage, turn_change, self_use, plural, other_usable, traits) FROM stdin;
9	charcoal	Charcoal	10	\N	f	Purges Poisons, Burns Merrily	t	t	\N	20	\N	t	\N	f	
10	sake	Sake	30	\N	f	Warms the Soul	t	t	\N	1	\N	t	\N	f	
11	mirror	Mirror Shard	120	\N	f	Reflects Light	t	t	\N	5	\N	t	\N	f	
12	shell	Shell Fragment	700	\N	f	Insulates against Flame	t	t	\N	0	\N	t	\N	f	
13	prayerwheel	Prayer Wheel	150	\N	f	Lifts Curses	t	t	\N	0	\N	t	\N	f	
15	sushi	Sushi	50	\N	f	For immediate consumption	t	t	\N	20	\N	t	\N	f	
16	fugu	Fugu Blowfish	50	\N	f	Delicious, or Deadly	t	t	\N	20	\N	t	\N	f	
17	oyoroi	O-yoroi Great Armor	3000	\N	f	Woven Armor with a Metal Breastplate against piercing and slashing	t	t	\N	3000	\N	t	s	f	
18	kozando	Kozan-do Scale Armor	1600	\N	f	Scale Plated Armor against slashing	t	t	\N	0	\N	t	s	f	
19	domaru	Do-Maru Woven Armor	1000	\N	f	Woven Armor against piercing and slashing	t	t	\N	0	\N	t	s	f	
20	tanko	Tanko Scale Armor	900	\N	f	Lamellate Armor against Crushing Blows	t	t	\N	0	\N	t	s	f	
21	tatamido	Tatami-do Folding Armor	1500	\N	f	Laced Squares of Flexible Leather for easy movement	t	t	\N	0	\N	t	s	f	
22	keikogi	Keiko-Gi Suit	70	\N	f	Thick Cloth Uniform for unfettered movement	t	t	\N	0	\N	t	s	f	
24	hakama	Hakama Garb	30	\N	f	Pleated, Loose Pants and Shirt for unfettered movement	t	t	\N	0	\N	t	s	f	
25	mask	Menpo Mask	600	\N	f	For Disguise or Intimidation	t	t	\N	0	\N	t	s	f	
27	meito	Meito Named Katana	3000	\N	f	Folded-Steel Named Sword for Slashing	t	f	\N	3000	\N	t	\N	f	
28	naginata	Naginata Spear	750	\N	f	Long Reached, Curved Spear for Piercing and Slashing	f	f	\N	750	\N	t	s	f	
30	kusarigama	Kusarigama Chain Sickle	500	\N	f	For Swinging Slashes and Entanglement	t	f	\N	500	\N	t	s	f	
32	tetsubo	Tetsubo Club	140	\N	f	For Piercing, Crushing Blows	f	f	\N	140	\N	t	\N	f	
33	nunchaku	Nunchaku	180	\N	f	Thrashing Blows with a long reach	t	f	\N	180	\N	t	\N	f	
34	zanbato	Zanbato Long Sword	660	\N	f	For Heavy Slashing Blows with a long reach	t	f	\N	660	\N	t	\N	f	
35	eku	Eku Wooden Oar	30	\N	f	For Slow, Wide-Arcing Blows	t	f	\N	130	\N	t	s	f	
36	ono	Ono Axe	10	\N	f	Great for Beheadings and De-limbing	f	f	\N	110	\N	t	s	f	
37	nekote	Neko-Te Claws	450	\N	f	For Poisoned slashing or climbing	f	t	\N	30	\N	t	\N	f	
23	kimono	Kimono	170	\N	f	Light Silk Clothing for formal wear	t	t	\N	0	\N	t	s	f	
26	katana	Katana	1800	\N	f	Crafted Sword for Slashing	f	f	\N	1800	\N	t	\N	f	
38	hamagari	Hamagari Saw	77	\N	f	For Poisoned slashing or climbing	f	t	\N	177	\N	t	s	f	
39	bo	Bo Staff	70	\N	f	For Ease of Walking	t	t	\N	170	\N	t	s	f	
14	lantern	Hooded Lantern	50	\N	f	A lantern for light and flame	t	t	\N	20	\N	t	\N	t	
31	kama	Kama Sickle	55	\N	f	For Reaping Rice	f	f	\N	55	\N	f	\N	f	
5	shuriken	Shuriken	50	mini_star.png	t	Reduces health	f	f	\N	\N	\N	f	\N	t	
3	amanita	Amanita Mushroom	225	mushroom.png	t	Increases Turns	t	t	\N	\N	6	t	s	t	
2	caltrops	Caltrops	125	caltrops.png	t	Reduces Turns	f	f	\N	\N	-6	f	\N	t	
4	smokebomb	Smoke Bomb	150	smoke_bomb.gif	t	Stealths a Ninja	f	f	\N	\N	\N	t	s	t	
6	dimmak	Dim Mak	1000	scroll.png	f	\N	t	t	\N	\N	\N	f	\N	t	
1	phosphor	Phosphor Powder	175	\N	t	Reduces health	f	f	\N	\N	\N	f	s	t	
7	ginsengroot	Ginseng Root	1000	\N	f	\N	t	t	\N	\N	\N	t	s	t	
8	tigersalve	Tiger Salve	3000	\N	f	\N	t	t	\N	\N	\N	t	s	t	
40	tessen	Tessen Fan	150	\N	t	For Cooling Air	f	t	\N	20	\N	f	s	t	
29	kunai	Kunai	50	\N	t	For Digging and Planting	t	t	\N	50	\N	f	\N	t	
\.


--
-- Data for Name: item_effects; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY item_effects (_item_id, _effect_id) FROM stdin;
1	2
1	7
1	1
2	13
2	12
2	1
7	16
8	17
3	14
6	22
6	1
5	10
5	1
4	15
29	1
29	12
40	1
\.


--
-- Name: item_item_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('item_item_id_seq', 40, true);


--
-- Data for Name: levelling_log; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY levelling_log (id, killpoints, levelling, killsdate, _player_id) FROM stdin;
\.


--
-- Name: levelling_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('levelling_log_id_seq', 4272480, true);


--
-- Data for Name: login_attempts; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY login_attempts (attempt_id, username, ua_string, ip, successful, additional_info, attempt_date) FROM stdin;
\.


--
-- Name: login_attempts_attempt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('login_attempts_attempt_id_seq', 67351, true);


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY messages (message_id, message, date, send_to, send_from, unread, type) FROM stdin;
\.


--
-- Name: messages_message_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('messages_message_id_seq', 344095, true);


--
-- Name: news_news_id_seq; Type: SEQUENCE SET; Schema: public; Owner: kzqai
--

SELECT pg_catalog.setval('news_news_id_seq', 1, false);


--
-- Data for Name: past_stats; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY past_stats (id, stat_type, stat_result) FROM stdin;
2	Most Kills Last Month	0
3	Total Kills Last Month	0
6	Total Kills Yesterday	0
1	Most Kills Yesterday	0
5	Previous Month's Vicious Killer	Tchalvak
4	Yesterday's Vicious Killer	Tchalvak
\.


--
-- Name: past_stats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('past_stats_id_seq', 1, false);


--
-- Data for Name: player_rank; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY player_rank (rank_id, _player_id, score) FROM stdin;
\.


--
-- Name: players_player_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('players_player_id_seq', 169565, true);


--
-- Name: skill_skill_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('skill_skill_id_seq', 16, true);



--
-- Data for Name: time; Type: TABLE DATA; Schema: public; Owner: developers
--

COPY "time" (time_id, time_label, amount) FROM stdin;
1	hours	9
\.


--
-- Name: time_time_id_seq; Type: SEQUENCE SET; Schema: public; Owner: developers
--

SELECT pg_catalog.setval('time_time_id_seq', 1, true);


--
-- PostgreSQL database dump complete
--


--
-- Data for Name: quests; Type: TABLE DATA; Schema: public; Owner: kzqai
--

COPY quests (quest_id, title, description, _player_id, tags, karma, rewards, obstacles, proof, expires_at, created_at, updated_at, type, difficulty) FROM stdin;
5	Some title	a desc	10	whatever,tags,ya	45	rwardify	obstacle	proof that I am great	2018-02-06 00:34:35.160892-05	2016-02-06 00:34:35.160892-05	2016-02-06 00:34:35.160892-05	\N	\N
\.


--
-- Name: quests_quest_id_seq; Type: SEQUENCE SET; Schema: public; Owner: kzqai
--

SELECT pg_catalog.setval('quests_quest_id_seq', 2, true);


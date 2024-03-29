<?php
$call_system = new headings_dewey();

class headings_dewey extends headings_static
{
    function headings_dewey()
    {
	$this->call_major_re = "/^([0-9]+)|^(B)/";

	$this->shelf_labels = array (
	    ''		    => '\d+',
	    'Biography'	    => 'B',
	    'Fiction'	    => '\[?F(ic)?\]?',
	    'Picture books' => '\[E\]'
	);

	$this->classifications = array (
	    '000' => 'Generalities',
	    '001' => 'Knowledge',
	    '002' => 'The book',
	    '003' => 'Systems',
	    '004' => 'Data processing, Computer science',
	    '005' => 'Computer programming, programs, data',
	    '006' => 'Special computer methods',
	    '010' => 'Bibliography',
	    '011' => 'Bibliographies',
	    '012' => 'Of individuals',
	    '013' => 'Of works by specific classes of authors',
	    '014' => 'Of anonymous and pseudonymous works',
	    '015' => 'Of works from specific places',
	    '016' => 'Of works on specific subjects',
	    '017' => 'General subject catalogs',
	    '018' => 'Catalogs arranged by author and date',
	    '019' => 'Dictionary catalogs',
	    '020' => 'Library & information sciences',
	    '021' => 'Relationships of libraries, archives, information centers',
	    '022' => 'Administration of the physical plant',
	    '023' => 'Personnel administration',
	    '025' => 'Library operations',
	    '026' => 'Libraries for specific subjects',
	    '027' => 'General libraries',
	    '028' => 'Reading and use of other information media',
	    '030' => 'General Encyclopedic works',
	    '031' => 'American',
	    '032' => 'In English',
	    '033' => 'In other Germanic languages',
	    '034' => 'In French, Proven�al, Catalon',
	    '035' => 'In Italian, Romanian, Rhaeto-Romanic',
	    '036' => 'In Spanish & Portuguese',
	    '037' => 'In Slavic Languages',
	    '038' => 'In Scandinavian languages',
	    '039' => 'In other languages',
	    '050' => 'General serials & their indexes',
	    '051' => 'American',
	    '052' => 'In English',
	    '053' => 'In other Germanic languages',
	    '054' => 'In French, Proven�al, Catalon',
	    '055' => 'In Italian, Romanian, Rhaeto-Romanic',
	    '056' => 'In Spanish & Portuguese',
	    '057' => 'In Slavic Languages',
	    '058' => 'In Scandinavian languages',
	    '059' => 'In other languages',
	    '060' => 'General organizations & museology',
	    '061' => 'In North America',
	    '062' => 'In British Isles; In England',
	    '063' => 'In central Europe; In Germany',
	    '064' => 'In France & Monaco',
	    '065' => 'In Italy & adjacent territories',
	    '066' => 'In Iberian Peninsula & adjacent islands',
	    '067' => 'In eastern Europe; In Soviet Union',
	    '068' => 'In other areas',
	    '069' => 'Museology (Museum science)',
	    '070' => 'News media, journalism, publishing',
	    '071' => 'In North America',
	    '072' => 'In British Isles; In England',
	    '073' => 'In central Europe; In Germany',
	    '074' => 'In France & Monaco',
	    '075' => 'In Italy & adjacent territories',
	    '076' => 'In Iberian Peninsula & adjacent islands',
	    '077' => 'In eastern Europe; In Soviet Union',
	    '078' => 'In Scandinavia',
	    '079' => 'In other areas',
	    '080' => 'General collections',
	    '081' => 'American',
	    '082' => 'In English',
	    '083' => 'In other Germanic languages',
	    '084' => 'In French, Proven�al, Catalon',
	    '085' => 'In Italian, Romanian, Rhaeto-Romanic',
	    '086' => 'In Spanish & Portuguese',
	    '087' => 'In Slavic Languages',
	    '088' => 'In Scandinavian languages',
	    '089' => 'In other languages',
	    '090' => 'Manuscripts and rare books',
	    '091' => 'Manuscripts',
	    '092' => 'Block books',
	    '093' => 'Incunabula',
	    '094' => 'Printed books',
	    '095' => 'Books notable for bindings',
	    '096' => 'Books notable for illustrations',
	    '097' => 'Books notable for ownership or origin',
	    '098' => 'Prohibited works, forgeries, hoaxes',
	    '099' => 'Books notable for format',
	    '100' => 'Philosophy & psychology',
	    '101' => 'Theory of philosophy',
	    '102' => 'Miscellany of philosophy',
	    '103' => 'Dictionaries of philosophy',
	    '103' => 'Essays',
	    '105' => 'Serial publications of philosophy',
	    '106' => 'Organizations of philosophy',
	    '107' => 'Education, research in philosophy',
	    '108' => 'Kinds of persons in philosophy',
	    '109' => 'Historical treatment of philosophy',
	    '110' => 'Metaphysics',
	    '111' => 'Ontology',
	    '113' => 'Cosmology',
	    '114' => 'Space',
	    '115' => 'Time',
	    '116' => 'Change',
	    '117' => 'Structure',
	    '118' => 'Force & energy',
	    '119' => 'Number & quantity',
	    '120' => 'Epistemology, causation, humankind',
	    '121' => 'Epistemology',
	    '122' => 'Causation',
	    '123' => 'Determinism & indeterminism',
	    '124' => 'Teleogy',
	    '126' => 'The self',
	    '127' => 'The unconscious & the subconscious',
	    '128' => 'Humankind',
	    '129' => 'Origin & destiny of individual souls',
	    '130' => 'Paranormal phenomena',
	    '131' => 'Occult methods for achieving well-being',
	    '133' => 'Parapsychology & occultism',
	    '135' => 'Dreams & mysteries',
	    '137' => 'Divinatory graphology',
	    '138' => 'Physiognomy',
	    '139' => 'Phrenology',
	    '140' => 'Specific philosophical schools',
	    '141' => 'Idealism & related systems',
	    '142' => 'Critical philosophy',
	    '143' => 'Intuitionism & Bergsonism',
	    '144' => 'Humanism & related systems',
	    '145' => 'Sensationalism',
	    '146' => 'Naturalism & related systems',
	    '147' => 'Pantheism & related systems',
	    '148' => 'Liberalism, eclecticism, traditionalism',
	    '149' => 'Other philosophical systems',
	    '150' => 'Psychology',
	    '152' => 'Perception, movement, emotions, drives',
	    '153' => 'Mental processes & intelligence',
	    '154' => 'Subconscious & altered states',
	    '155' => 'Differential & developmental psychology',
	    '156' => 'Comparative psychology',
	    '158' => 'Applied psychology',
	    '160' => 'Logic.',
	    '161' => 'Induction',
	    '162' => 'Deduction',
	    '165' => 'Fallacies & sources of error',
	    '166' => 'Syllogisms',
	    '167' => 'Hypotheses',
	    '168' => 'Argument & persuasion',
	    '169' => 'Analogy',
	    '170' => 'Ethics',
	    '171' => 'Systems & doctrines',
	    '172' => 'Political ethics',
	    '173' => 'Ethics of family relationships',
	    '174' => 'Economic & professional ethics',
	    '175' => 'Ethics of recreation & leisure',
	    '176' => 'Ethics of sex & reproduction',
	    '177' => 'Ethics of social relations',
	    '178' => 'Ethics of consumption',
	    '179' => 'Other ethical norms',
	    '180' => 'Ancient, medi�val, Oriental philosophy',
	    '181' => 'Oriental philosophy',
	    '182' => 'Pre-Socratic Greek philosophies',
	    '183' => 'Sophistic & Socratic philosophies',
	    '184' => 'Platonic philosophy',
	    '185' => 'Aristotelian philosophy',
	    '186' => 'Skeptic and Neoplatonic philosophies',
	    '187' => 'Epicurean philosophy',
	    '188' => 'Stoic philosophy',
	    '189' => 'Medieval Western philosophy',
	    '190' => 'Modern Western philosophy',
	    '191' => 'United States & Canada',
	    '192' => 'British Isles',
	    '193' => 'Germany & Austria',
	    '194' => 'France',
	    '195' => 'Italy',
	    '196' => 'Spain & Portugal',
	    '197' => 'Soviet Union',
	    '198' => 'Scandinavia',
	    '199' => 'Other geographical areas',
	    '200' => 'Religion',
	    '201' => 'Philosophy of Christianity',
	    '202' => 'Miscellany of Christianity',
	    '203' => 'Dictionaries of Christianity',
	    '204' => 'Special topics',
	    '205' => 'Serial publications of Christianity',
	    '206' => 'Organizations of Christianity',
	    '207' => 'Education, research in Christianity',
	    '208' => 'Kinds of persons in Christianity',
	    '209' => 'History & geography of Christianity',
	    '210' => 'Natural theology',
	    '211' => 'Concepts of God',
	    '212' => 'Existence, attributes of God',
	    '213' => 'Creation',
	    '214' => 'Theodicy',
	    '215' => 'Science & religion',
	    '216' => 'Good & evil',
	    '218' => 'Humankind',
	    '220' => 'Bible',
	    '221' => 'Old Testament',
	    '222' => 'Historical books of Old Testament',
	    '223' => 'Poetic books of Old Testament',
	    '224' => 'Prophetic books of Old Testament',
	    '225' => 'New Testament',
	    '226' => 'Gospels & Acts',
	    '227' => 'Epistles',
	    '228' => 'Revelation (Apocalypse)',
	    '229' => 'Apocrypha & pseudepigrapha',
	    '230' => 'Christian theology',
	    '231' => 'God',
	    '232' => 'Jesus Christ & his family',
	    '233' => 'Humankind',
	    '234' => 'Salvation (Soteriology) & grace',
	    '235' => 'Spiritual beings',
	    '236' => 'Eschatology',
	    '238' => 'Creeds & catechisms',
	    '239' => 'Apologetics & polemics',
	    '240' => 'Christian moral & devotional theology',
	    '241' => 'Moral theology',
	    '242' => 'Devotional literature',
	    '243' => 'Evangelistic writings for individuals',
	    '245' => 'Texts of hymns',
	    '246' => 'Use of art in Christianity',
	    '247' => 'Church furnishings & articles',
	    '248' => 'Christian experience, practice, life',
	    '249' => 'Christian observances in family life',
	    '250' => 'Christian orders & local church',
	    '251' => 'Preaching (Homiletics)',
	    '252' => 'Texts of sermons',
	    '253' => 'Pastoral office (Pastoral theology)',
	    '254' => 'Parish goverment & administration',
	    '255' => 'Religious congregations & orders',
	    '259' => 'Activities of the local church',
	    '260' => 'Christian social theology',
	    '261' => 'Social theology',
	    '262' => 'Ecclesiology',
	    '263' => 'Times, places of religious observance',
	    '264' => 'Public worship',
	    '265' => 'Sacraments, other rites & acts',
	    '266' => 'Missions',
	    '267' => 'Associations for religious work',
	    '268' => 'Religious education',
	    '269' => 'Spiritual renewal',
	    '270' => 'Christian church history',
	    '271' => 'Religious orders in church history',
	    '272' => 'Persecutions in church history',
	    '273' => 'Heresies in church history',
	    '274' => 'Christian church in Europe',
	    '275' => 'Christian church in Asia',
	    '276' => 'Christian church in Africa',
	    '277' => 'Christian church in North America',
	    '278' => 'Christian church in South America',
	    '279' => 'Christian church in other areas',
	    '280' => 'Christian denominations & sects',
	    '281' => 'Early church & Eastern churches',
	    '282' => 'Roman Catholic Church',
	    '283' => 'Anglican churches',
	    '284' => 'Protestants of Continental origin',
	    '285' => 'Presbyterian, Reformed, Congregational',
	    '286' => 'Baptist, Disciples of Christ, Adventist',
	    '287' => 'Methodist churches; churches uniting Methodist and other denominations; Salvation Army',
	    '289' => 'Other denominations & sects',
	    '290' => 'Other & comparative religions',
	    '291' => 'Comparative religion',
	    '292' => 'Classical (Greek & Roman) religion',
	    '293' => 'Germanic religion',
	    '294' => 'Religions of Indic origin',
	    '295' => 'Zoroastrianism (Mazdaism, Parseeism)',
	    '296' => 'Judaism',
	    '297' => 'Islam & religions originating in it',
	    '299' => 'Other religions',
	    '300' => 'Social Sciences',
	    '301' => 'Sociology & anthropology',
	    '302' => 'Social interaction',
	    '303' => 'Social processes',
	    '304' => 'Factors affecting social behavior',
	    '305' => 'Social groups',
	    '306' => 'Culture & institutions',
	    '307' => 'Communities',
	    '309' => 'History',
	    '310' => 'General statistics',
	    '314' => 'General statistics of Europe',
	    '315' => 'General statistics of Asia',
	    '316' => 'General statistics of Africa',
	    '317' => 'General statistics of North America',
	    '318' => 'General statistics of South America',
	    '319' => 'General statistics of other parts of the world',
	    '320' => 'Political science',
	    '321' => 'Systems of governments & states',
	    '322' => 'Relation of state to organized groups',
	    '323' => 'Civil & political rights',
	    '324' => 'The political process',
	    '325' => 'International migration & colonization',
	    '326' => 'Slavery & emancipation',
	    '327' => 'International relations',
	    '328' => 'The legislative process',
	    '330' => 'Economics',
	    '331' => 'Labor economics',
	    '332' => 'Financial economics',
	    '333' => 'Land economics',
	    '334' => 'Cooperatives',
	    '335' => 'Socialism & related systems',
	    '336' => 'Public finance',
	    '337' => 'International economics',
	    '338' => 'Production',
	    '339' => 'Macroeconomics & related topics',
	    '340' => 'Law',
	    '341' => 'International law',
	    '342' => 'Constitutional & administrative law',
	    '343' => 'Military, tax, trade, industrial law',
	    '344' => 'Social, labor, welfare, & related law',
	    '345' => 'Criminal law',
	    '346' => 'Private law',
	    '347' => 'Civil procedure & courts',
	    '348' => 'Laws (Statutes), regulations, cases',
	    '349' => 'Laws of specific jurisdictions & areas',
	    '350' => 'Public administration',
	    '351' => 'Public administration of central governments',
	    '352' => 'Public administration of local governments',
	    '353' => 'Public administration of U.S. federal & state governments',
	    '354' => 'Public administration of specific central governments',
	    '355' => 'Military science',
	    '356' => 'Foot forces & warfare',
	    '357' => 'Mounted forces & warfare',
	    '358' => 'Other specialized forces & services',
	    '359' => 'Sea (Naval) forces & warfare',
	    '360' => 'Social services; association',
	    '361' => 'General social problems & welfare',
	    '362' => 'Social welfare problems & welfare',
	    '363' => 'Other social problems & services',
	    '364' => 'Criminology',
	    '365' => 'Penal & related institutions',
	    '366' => 'Association',
	    '367' => 'General clubs',
	    '368' => 'Insurance',
	    '369' => 'Miscellaneous kinds of associations',
	    '370' => 'Education',
	    '371' => 'School manaagement; special education',
	    '372' => 'Elementary education',
	    '373' => 'Secondary education',
	    '374' => 'Adult edutation',
	    '375' => 'Curricula',
	    '376' => 'Education of women',
	    '377' => 'Schools & religion',
	    '378' => 'Higher education',
	    '379' => 'Government regulation, control, support',
	    '380' => 'Commerce, communications, transport',
	    '381' => 'Internal commerce (Domestic trade)',
	    '382' => 'International commerce (Foreign trade)',
	    '383' => 'Postal communications',
	    '384' => 'Communications; Telecommunications',
	    '385' => 'Railroad transportation',
	    '386' => 'Inland waterway & ferry transportation',
	    '387' => 'Water, air, space transportation',
	    '388' => 'Transportation; Ground transportation',
	    '389' => 'Metrology & standardization',
	    '390' => 'Customs, etiquette, folklore',
	    '391' => 'Costume & personal appearance',
	    '392' => 'Customs of life cycle & domestic life',
	    '393' => 'Death customs',
	    '394' => 'General customs',
	    '395' => 'Etiquette (Manners)',
	    '398' => 'Folklore',
	    '399' => 'Customs of war & diplomacy',
	    '400' => 'Language',
	    '401' => 'Philosophy & theory',
	    '402' => 'Miscellany',
	    '403' => 'Dictionaries & encyclopedias',
	    '404' => 'Special topics',
	    '405' => 'Serial publications',
	    '406' => 'Organizations & management',
	    '407' => 'Education, research, related topics',
	    '408' => 'Treatment of language with respect to kinds of persons',
	    '409' => 'Geographical & persons treatment',
	    '410' => 'Linguistics',
	    '411' => 'Writing systems',
	    '412' => 'Etymology',
	    '413' => 'Dictionaries',
	    '414' => 'Phonology',
	    '415' => 'Structural systems (Grammar)',
	    '417' => 'Dialectology & historicl linguistics',
	    '418' => 'Standard usage; Applied linguistics',
	    '419' => 'Verbal language not spoken or written',
	    '420' => 'English & Old English',
	    '421' => 'English writing system & phonology',
	    '422' => 'English etymology',
	    '423' => 'English dictionaries',
	    '425' => 'English grammar',
	    '427' => 'English language variations',
	    '428' => 'Standard English usage',
	    '429' => 'Old English (Anglo-Saxon)',
	    '430' => 'Germanic languages; German',
	    '431' => 'German writing system & phonology',
	    '432' => 'German etymology',
	    '433' => 'German dictionaries',
	    '435' => 'German grammar',
	    '437' => 'German language variations',
	    '438' => 'Standard German usage',
	    '439' => 'Other Germanic languages',
	    '440' => 'Romance languages; French',
	    '441' => 'French writing system & phonology',
	    '442' => 'French etymology',
	    '443' => 'French dictionaries',
	    '445' => 'French grammar',
	    '447' => 'French language variations',
	    '448' => 'Standard French usage',
	    '449' => 'Proven�al & Catalan',
	    '450' => 'Italian, Romanian, Rh�to-Romanic',
	    '451' => 'Italian writing system & phonology',
	    '452' => 'Italian etymology',
	    '453' => 'Italian dictionaries',
	    '455' => 'Italian grammar',
	    '457' => 'Italian language variations',
	    '458' => 'Standard Italian usage',
	    '459' => 'Romanian & Rh�to-Romanic',
	    '460' => 'Spanish & Portuguese languages',
	    '461' => 'Spanish writing system & phonology',
	    '462' => 'Spanish etymology',
	    '463' => 'Spanish dictionaries',
	    '465' => 'Spanish grammar',
	    '467' => 'Spanish language variations',
	    '468' => 'Standard Spanish usage',
	    '469' => 'Portuguese',
	    '470' => 'Italic languages; Latin',
	    '471' => 'Classical Latin writing system & phonology',
	    '472' => 'Classical Latin etymology',
	    '473' => 'Classical Latin dictionaries',
	    '475' => 'Classical Latin grammar',
	    '477' => 'Old, Postclassical, Vulgar Latin',
	    '478' => 'Classical Latin usage',
	    '479' => 'Other Italic languages',
	    '480' => 'Hellenic languages; Classical Greek',
	    '481' => 'Classical Greek writing system & phonology',
	    '482' => 'Classical Greek etymology',
	    '483' => 'Classical Greek dictionaries',
	    '485' => 'Classical Greek grammar',
	    '487' => 'Classical Greek language variations',
	    '488' => 'Classical Greek usage',
	    '489' => 'Other Hellenic languages',
	    '490' => 'Other languages',
	    '491' => 'East Indo-European & Celtic languages',
	    '492' => 'Afro-Asiatic languages; Semitic',
	    '493' => 'Non-Semitic Afro-Asiatic languages',
	    '494' => 'Ural-Altaic, Paleosiberian, Dravidian',
	    '495' => 'Languages of East & Southeast Asia',
	    '496' => 'African languages',
	    '497' => 'North American native languages',
	    '498' => 'South American native languages',
	    '499' => 'Miscellaneous languages',
	    '500' => 'Natural sciences & mathematics',
	    '501' => 'Philosophy & theory',
	    '502' => 'Miscellany',
	    '503' => 'Dictionaries & encyclopedias',
	    '505' => 'Serial publications',
	    '506' => 'Organizations & management',
	    '507' => 'Education, research, related topics',
	    '508' => 'Natural history',
	    '509' => 'Historical, areas, persons treatment',
	    '510' => 'Mathematics',
	    '511' => 'General topics',
	    '512' => 'Algebra & number theory',
	    '513' => 'Arithmetic',
	    '514' => 'Topology',
	    '515' => 'Analysis',
	    '516' => 'Geometry',
	    '519' => 'Probabilities & applied mathematics',
	    '520' => 'Astronomy & allied sciences',
	    '521' => 'Celestial mechanics',
	    '522' => 'Techniques, equipment, materials',
	    '523' => 'Specific celestial bodies & phenomena',
	    '525' => 'Earth (Astronomical geography)',
	    '526' => 'Mathematical geography',
	    '527' => 'Celestial navigation',
	    '528' => 'Ephemerides',
	    '529' => 'Chronology',
	    '530' => 'Physics',
	    '531' => 'Classical mechanics; Solid mechanics',
	    '532' => 'Fluid mechanics; Liquid mechanics',
	    '533' => 'Gas mechanics',
	    '534' => 'Sound & related vibrations',
	    '535' => 'Light & related radiations',
	    '536' => 'Heat',
	    '537' => 'Electriticy & electronics',
	    '538' => 'Magnetism',
	    '539' => 'Modern physics',
	    '540' => 'Chemistry & allied sciences',
	    '541' => 'Physical & theoretical chemistry',
	    '542' => 'Techniques, equipment, materials',
	    '543' => 'Analytical chemistry',
	    '544' => 'Qualitative analysis',
	    '545' => 'Quantitative analysis',
	    '546' => 'Inorganic chemistry',
	    '547' => 'Organic chemistry',
	    '548' => 'Crystallography',
	    '549' => 'Mineralogy',
	    '550' => 'Earth sciences',
	    '551' => 'Geology, hydrology, meteorology',
	    '552' => 'Petrology',
	    '553' => 'Economic geology',
	    '554' => 'Earth sciences of Europe',
	    '555' => 'Earth sciences of Asia',
	    '556' => 'Earth sciences of Africa',
	    '557' => 'Earth sciences of North America',
	    '558' => 'Earth sciences of South America',
	    '559' => 'Earth sciences of other areas',
	    '560' => 'Paleontology; Paleozoology',
	    '561' => 'Paleobotany',
	    '562' => 'Fossil invertebrates',
	    '563' => 'Fossil primitive phyla',
	    '564' => 'Fossil mollusks & related phyla',
	    '565' => 'Other fossil invertebrates',
	    '566' => 'Fossil vertebrates',
	    '567' => 'Fossil cold-blooded vertebrates',
	    '568' => 'Fossil birds',
	    '569' => 'Fossil mammals',
	    '570' => 'Life sciences',
	    '572' => 'Human races',
	    '573' => 'Physical anthropology',
	    '574' => 'Biology',
	    '575' => 'Evolution & genetics',
	    '576' => 'Microbiology',
	    '577' => 'General nature of life',
	    '578' => 'Microscopy in biology',
	    '579' => 'Collection and preservation',
	    '580' => 'Botanical sciences',
	    '581' => 'Botany',
	    '582' => 'Seed-bearing plants',
	    '583' => 'Dicotyledons',
	    '584' => 'Monocotyledons',
	    '585' => 'Gymnosperms (Naked-seed plants)',
	    '586' => 'Seedless plants',
	    '587' => 'Pteridophyta (Vascular seedless plants)',
	    '588' => 'Bryophytes',
	    '589' => 'Thallophytes & prokaryotes',
	    '590' => 'Zoological sciences',
	    '591' => 'Zoology',
	    '592' => 'Invertebrates',
	    '593' => 'Protozoa, Echinodermata, related phyla',
	    '594' => 'Mollusks & related phyla',
	    '595' => 'Other invertebrates',
	    '596' => 'Vertebrates',
	    '597' => 'Cold-blooded vertebrates',
	    '598' => 'Birds',
	    '599' => 'Mammals',
	    '600' => 'Technology (Applied sciences)',
	    '601' => 'Philosophy & theory',
	    '602' => 'Miscellany',
	    '603' => 'Dictionaries & encyclopedias',
	    '604' => 'Special topics',
	    '605' => 'Serial publications',
	    '606' => 'Organizations',
	    '607' => 'Education, research, related topics',
	    '608' => 'Invention & patents',
	    '609' => 'Historical, areas, persons treatment',
	    '610' => 'Medical sciences; Medicine',
	    '611' => 'Human anatomy, cytology, histology',
	    '612' => 'Human physiology',
	    '613' => 'Promotion of health',
	    '614' => 'Incidence & prevention of disease',
	    '615' => 'Pharmacology & therapeutics',
	    '616' => 'Diseases',
	    '617' => 'Surgery & related medical specialties',
	    '618' => 'Gynecology & other medical specialties',
	    '619' => 'Experimental medicine',
	    '620' => 'Engineering & allied operations',
	    '621' => 'Applied physics',
	    '622' => 'Mining & related operations',
	    '623' => 'Military & nautical engineering',
	    '624' => 'Civil engineering',
	    '625' => 'Engineering of railroads, roads',
	    '627' => 'Hydraulic engineering',
	    '628' => 'Sanitary & municipal engineering     Environmental protection engineering',
	    '629' => 'Other branches of engineering',
	    '630' => 'Agriculture',
	    '631' => 'Techniques, equipment, materials',
	    '632' => 'Plant injuries, diseases, pests',
	    '633' => 'Field & plantation crops',
	    '634' => 'Orchards, fruits, forestry',
	    '635' => 'Garden crops (Horticulture)',
	    '636' => 'Animal husbandry',
	    '637' => 'Processing dairy & related products',
	    '638' => 'Insect culture',
	    '639' => 'Hunting, fishing, conservation',
	    '640' => 'Home economics & family living',
	    '641' => 'Food & drink',
	    '642' => 'Meals & table service',
	    '643' => 'Housing & household equipment',
	    '644' => 'Household utilities',
	    '645' => 'Household furnishings',
	    '646' => 'Sewing, clothing, personal living',
	    '647' => 'Management of public households',
	    '648' => 'Housekeeping',
	    '649' => 'Child rearing & home care of sick',
	    '650' => 'Management & auxiliary services',
	    '651' => 'Office services',
	    '652' => 'Processes of written communication',
	    '653' => 'Shorthand',
	    '657' => 'Accounting',
	    '658' => 'General management',
	    '659' => 'Advertising & public relations',
	    '660' => 'Chemical engineering',
	    '661' => 'Industrial chemicals technology',
	    '662' => 'Explosives, fuels technology',
	    '663' => 'Beverage technology',
	    '664' => 'Food technology',
	    '665' => 'Industrial oils, fats, waxes, gases',
	    '666' => 'Ceramic & allied technologies',
	    '667' => 'Cleaning, color, related technologies',
	    '668' => 'Technology of other organic products',
	    '669' => 'Metallurgy',
	    '670' => 'Manufacturing',
	    '671' => 'Metalworking & metal products',
	    '672' => 'Iron, steel, other iron alloys',
	    '673' => 'Nonferrous metals',
	    '674' => 'Lumber processing, wood products, cork',
	    '675' => 'Leather & fur processing',
	    '676' => 'Pulp & paper technology',
	    '677' => 'Textiles',
	    '678' => 'Elastomers & elastomer products',
	    '679' => 'Other products of specific materials',
	    '680' => 'Manufacture for specific uses',
	    '681' => 'Precision instruments & other devices',
	    '682' => 'Small forge work (Blacksmithing)',
	    '683' => 'Hardware & household appliences',
	    '684' => 'Furnishings & home workshops',
	    '685' => 'Leather, fur, related products',
	    '686' => 'Printing & related activities',
	    '687' => 'Clothing',
	    '688' => 'Other final products & packaging',
	    '690' => 'Buildings',
	    '691' => 'Building materials',
	    '692' => 'Auxiliary construction practices',
	    '693' => 'Specific materials & purposes',
	    '694' => 'Wood construction; Carpentry',
	    '695' => 'Roof covering',
	    '696' => 'Utilities',
	    '697' => 'Heating, ventilation, air-conditioning',
	    '698' => 'Detail finishing',
	    '700' => 'The arts',
	    '701' => 'Philosophy & theory',
	    '702' => 'Miscellany',
	    '703' => 'Dictionaries & encyclopedias',
	    '704' => 'Special topics',
	    '705' => 'Serial publications',
	    '706' => 'Organizations & management',
	    '707' => 'Education, research, related topics',
	    '708' => 'Galleries, museums, private collections',
	    '709' => 'Historical, areas, persons treatment',
	    '710' => 'Civic & landscape art',
	    '711' => 'Area planning (Civic art)',
	    '712' => 'Landscape architecture',
	    '713' => 'Landscape architecture of trafficways',
	    '714' => 'Water features',
	    '715' => 'Woody plants',
	    '716' => 'Herbaceous plants',
	    '717' => 'Structures',
	    '718' => 'Landscape design of cemeteries',
	    '719' => 'Natural landscapes',
	    '720' => 'Architecture',
	    '721' => 'Architectural structure',
	    '722' => 'Architecture to ca. 300',
	    '723' => 'Architecture from ca. 300 to 1399',
	    '724' => 'Architecture from 1400',
	    '725' => 'Public structures',
	    '726' => 'Buildings for religious purposes',
	    '727' => 'Buildings for education & research',
	    '728' => 'Residential & related buildings',
	    '729' => 'Design & decoration',
	    '730' => 'Plastic arts; Sculpture',
	    '731' => 'Processes, forms, subjects of sculpture',
	    '732' => 'Sculpture to ca. 500',
	    '733' => 'Greek, Etruscan, Roman sculpture',
	    '734' => 'Sculpture from ca. 500 to 1399',
	    '735' => 'Sculpture from 1400',
	    '736' => 'Carving & carvings',
	    '737' => 'Numismatics & sigillography',
	    '738' => 'Ceramic arts',
	    '739' => 'Art metalwork',
	    '740' => 'Drawing & decorative arts',
	    '741' => 'Drawing & drawings',
	    '742' => 'Perspective',
	    '743' => 'Drawing & drawings by subject',
	    '745' => 'Decorative arts',
	    '746' => 'Textile arts',
	    '747' => 'Interior decoration',
	    '748' => 'Glass',
	    '749' => 'Furniture & accessories',
	    '750' => 'Painting & paintings',
	    '751' => 'Techniques, equipment, forms',
	    '752' => 'Color',
	    '753' => 'Symbolism, allegory, mythology, legend',
	    '754' => 'Genre paintings',
	    '755' => 'Religion & religious symbolism',
	    '757' => 'Human figures & their parts',
	    '758' => 'Other subjects',
	    '759' => 'Historical, areas, persons treatment',
	    '760' => 'Graphic arts; Printmaking & prints',
	    '761' => 'Relief processes (Block printing)',
	    '763' => 'Lithographic (Planographic) processes',
	    '764' => 'Chromolithography & serigraphy',
	    '765' => 'Metal engraving',
	    '766' => 'Mezzotinting & related processes',
	    '767' => 'Etching & drypoint',
	    '769' => 'Prints',
	    '770' => 'Photography & photographs',
	    '771' => 'Techniques, equipment, materials',
	    '772' => 'Metallic salt processes',
	    '773' => 'Pigment processes of printing',
	    '774' => 'Holography',
	    '778' => 'Fields & kinds of photography',
	    '779' => 'Photographs',
	    '780' => 'Music',
	    '781' => 'General principles & musical forms',
	    '782' => 'Vocal music',
	    '783' => 'Music for single voices; The voice',
	    '784' => 'Instruments & instrumental ensembles',
	    '785' => 'Chamber music',
	    '786' => 'Keyboard & other instruments',
	    '787' => 'Stringed instruments',
	    '788' => 'Wind instruments',
	    '790' => 'Recreational & performing arts',
	    '791' => 'Public performances',
	    '792' => 'Stage presentations',
	    '793' => 'Indoor games & amusements',
	    '794' => 'Indoor games of skill',
	    '795' => 'Games of chance',
	    '796' => 'Athletic & outdoor sports & games',
	    '797' => 'Aquatic & air sports',
	    '798' => 'Equestrian sports & animal racing',
	    '799' => 'Fishing, hunting, shooting',
	    '800' => 'Literature and rhetoric',
	    '801' => 'Philosophy & theory',
	    '802' => 'Miscellany',
	    '803' => 'Dictionaries & encyclopedias',
	    '805' => 'Serial publications',
	    '806' => 'Organizations',
	    '807' => 'Education, research, related topics',
	    '808' => 'Rhetoric & collections of literature',
	    '809' => 'Literary history & criticism',
	    '810' => 'American literature in English',
	    '811' => 'Poetry',
	    '812' => 'Drama',
	    '813' => 'Fiction',
	    '814' => 'Essays',
	    '815' => 'Speeches',
	    '816' => 'Letters',
	    '817' => 'Satire & humor',
	    '818' => 'Miscellaneous writings',
	    '820' => 'English & Old English literatures',
	    '821' => 'English poetry',
	    '822' => 'English drama',
	    '823' => 'English fiction',
	    '824' => 'English essays',
	    '825' => 'English speeches',
	    '826' => 'English letters',
	    '827' => 'English satire & humor',
	    '828' => 'English miscellaneous writings',
	    '829' => 'Old English (Anglo-Saxon)',
	    '830' => 'Literatures of Germanic languages',
	    '831' => 'German poetry',
	    '832' => 'German drama',
	    '833' => 'German fiction',
	    '834' => 'German essays',
	    '835' => 'German speeches',
	    '836' => 'German letters',
	    '837' => 'German satire & humor',
	    '838' => 'German miscellaneous writings',
	    '839' => 'Other Germanic literatures',
	    '840' => 'Literatures of Romance languages',
	    '841' => 'French poetry',
	    '842' => 'French drama',
	    '843' => 'French fiction',
	    '844' => 'French essays',
	    '845' => 'French speeches',
	    '846' => 'French letters',
	    '847' => 'French satire & humor',
	    '848' => 'French miscellaneous writings',
	    '849' => 'Proven�al & Catalan',
	    '850' => 'Italian, Romanian, Rh�to-Romanic',
	    '851' => 'Italian poetry',
	    '852' => 'Italian drama',
	    '853' => 'Italian fiction',
	    '854' => 'Italian essays',
	    '855' => 'Italian speeches',
	    '856' => 'Italian letters',
	    '857' => 'Italian satire & humor',
	    '858' => 'Italian miscellaneous writings',
	    '859' => 'Romanian & Rh�to-Romanic',
	    '860' => 'Spanish & Portuguese literatuers',
	    '861' => 'Spanish poetry',
	    '862' => 'Spanish drama',
	    '863' => 'Spanish fiction',
	    '864' => 'Spanish essays',
	    '865' => 'Spanish speeches',
	    '866' => 'Spanish letters',
	    '867' => 'Spanish satire & humor',
	    '868' => 'Spanish miscellaneous writings',
	    '869' => 'Portuguese',
	    '870' => 'Latin & Old Latin literatuers',
	    '871' => 'Latin poetry',
	    '872' => 'Latin drama',
	    '873' => 'Latin fiction',
	    '874' => 'Latin essays',
	    '875' => 'Latin speeches',
	    '876' => 'Latin letters',
	    '877' => 'Latin satire & humor',
	    '878' => 'Latin miscellaneous writings',
	    '879' => 'Literatures of other Italic languages',
	    '880' => 'Hellenic literatures; Classical Greek',
	    '881' => 'Classical Greek poetry',
	    '882' => 'Classical Greek drama',
	    '883' => 'Classical Greek fiction',
	    '884' => 'Classical Greek essays',
	    '885' => 'Classical Greek speeches',
	    '886' => 'Classical Greek letters',
	    '887' => 'Classical Greek satire & humor',
	    '888' => 'Classical Greek miscellaneous writings',
	    '889' => 'Modern Greek',
	    '890' => 'Literatures of other languages',
	    '891' => 'East Indo-European & Celtic literatures',
	    '892' => 'Afro-Asiatic literatures; Semitic',
	    '893' => 'Non-Semitic Afro-Asiatic literatures',
	    '894' => 'Ural-Altaic, Paleosiberian, Dravidian',
	    '895' => 'Literatures of East & Southeast Asia',
	    '896' => 'African literatures',
	    '897' => 'North American native literatures',
	    '898' => 'South American native literatures',
	    '899' => 'Other literatures',
	    '900' => 'Geography & history',
	    '901' => 'Philosophy & theory',
	    '902' => 'Miscellany',
	    '903' => 'Dictionaries & encyclopedias',
	    '904' => 'Collected accounts of events',
	    '905' => 'Serial publications',
	    '906' => 'Organizations & management',
	    '907' => 'Education, research, related topics',
	    '908' => 'With respect to kinds of persons',
	    '909' => 'World history',
	    '910' => 'Geography & travel',
	    '911' => 'Historical geography',
	    '912' => 'Graphic representations of earth',
	    '913' => 'Ancient world',
	    '914' => 'Europe',
	    '915' => 'Asia',
	    '916' => 'Africa',
	    '917' => 'North America',
	    '918' => 'South America',
	    '919' => 'Other areas',
	    '920' => 'Biography, genealogy, insignia',
	    '921' => 'Biography of philosophy',
	    '922' => 'Biography of theology',
	    '923' => 'Biography of sociology',
	    '924' => 'Biography of philology',
	    '925' => 'Biography of science',
	    '926' => 'Biography of practical arts',
	    '927' => 'Biography of fine arts',
	    '928' => 'Biography of literature',
	    '929' => 'Genealogy, names, insignia',
	    '930' => 'History of ancient world',
	    '931' => 'China',
	    '932' => 'Egypt',
	    '933' => 'Palestine',
	    '934' => 'India',
	    '935' => 'Mesopotamia & Iranian Plateau',
	    '936' => 'Europe north & west of Italy',
	    '937' => 'Italy & adjacent territories',
	    '938' => 'Greece',
	    '939' => 'Other parts of ancient world',
	    '940' => 'General history of Europe',
	    '941' => 'British Isles',
	    '942' => 'England & Wales',
	    '943' => 'Central Europe; Germany',
	    '944' => 'France & Monaco',
	    '945' => 'Italian Peninsula & adjacent islands',
	    '946' => 'Iberian Peninsula & adjacent islands',
	    '947' => 'Eastern Europe; Soviet Union',
	    '948' => 'Northern Europe; Scandinavia',
	    '949' => 'Other parts of Europe',
	    '950' => 'General history of Asia; Far East',
	    '951' => 'China & adjacent areas',
	    '952' => 'Japan',
	    '953' => 'Arabian Peninsula & adjacent areas',
	    '954' => 'South Asia; India',
	    '955' => 'Iran',
	    '956' => 'Middle East (Near East)',
	    '957' => 'Siberia (Asiatic Russia)',
	    '958' => 'Central Asia',
	    '959' => 'Southeast Asia',
	    '960' => 'General history of Africa',
	    '961' => 'Tunisia & Libya',
	    '962' => 'Egypt & Sudan',
	    '963' => 'Ethiopia',
	    '964' => 'Morocco & Canary Islands',
	    '965' => 'Algeria',
	    '966' => 'West Africa & offshore islands',
	    '967' => 'Central Africa & offshore islands',
	    '968' => 'Southern Africa',
	    '969' => 'South Indian Ocean islands',
	    '970' => 'General history of North America',
	    '971' => 'Canada',
	    '972' => 'Middle America; Mexico',
	    '973' => 'United States',
	    '974' => 'Northeastern United States',
	    '975' => 'Southeastern United States',
	    '976' => 'South central United States',
	    '977' => 'North central United States',
	    '978' => 'Western United States',
	    '979' => 'Great Basin & Pacific Slope',
	    '980' => 'General history of South America',
	    '981' => 'Brazil',
	    '982' => 'Argentina',
	    '983' => 'Chile',
	    '984' => 'Bolivia',
	    '985' => 'Peru',
	    '986' => 'Colombia & Ecuador',
	    '987' => 'Venezuela',
	    '988' => 'Guiana',
	    '989' => 'Paraguay & Uruguay',
	    '990' => 'General history of other areas',
	    '993' => 'New Zealand',
	    '994' => 'Australia',
	    '995' => 'Melanesia; New Guinea',
	    '996' => 'Other parts of Pacific; Polynesia',
	    '997' => 'Atlantic Ocean islands',
	    '998' => 'Arctic islands & Antarctica',
	    '999' => 'Extraterrestrial worlds'
	);
    }
}

?>

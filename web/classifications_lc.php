<?php
$call_system = new headings_lc();

class headings_lc extends headings_static
{
    function headings_lc()
    {
	$this->call_major_re = "/^([a-zA-Z]+)/";

	$this->shelf_labels = array (
	    'Reference'				    => 'A.*',
	    'Philosophy'			    => 'B|B[A-DHJ]',
	    'Psychology'			    => 'BF',
	    'Religion'				    => 'B[L-X]',
	    'History'				    => '[C-F]',
	    'Geography'				    => 'G.*',
	    'Economics'				    => 'H[B-J]',
	    'Sociology'				    => 'H[M-V]',
	    'Anarchism'				    => 'HX(82[1-9]|8[3-9][0-9]|9[0-9][0-9])',
	    'Socialism'				    => "HX([1-9]|[1-9][0-9]|[1-7][0-9][0-9]|8[0-1][0-9]|820)[.]",
	    'Political Science'			    => 'J.*',
	    'Law'				    => 'K.*',
	    'Education'				    => 'L.*',
	    'Music'	    	    		    => 'M.*',
	    'Visual Arts'   	    		    => 'N.|TR|TT',
	    'Linguistics'   	    		    => 'P[A-M]?[1-9]',
	    'Literature'    	    		    => 'P[N-Z]',
	    'Math'	    	    		    => 'HA|QA',
	    'Life Sciences'	    		    => 'Q[H-R]',
	    'Physical Sciences'	    		    => 'Q[B-E]?', // not quite true, Q is gen sci
	    'Medicine'		    		    => 'R.*',
	    'Agriculture'	    		    => 'S[BD]?',
	    'Animal Culture'	    		    => 'S[FHK]',
	    'Technology'	    		    => 'T[A-K]?',
	    'Military'		    		    => 'U',
	    'About books'	    		    => 'Z.*'
	);

	$this->classifications = array (
	    'AC'	=> 'Collections. Series. Collected works',
	    'AE'	=> 'Encyclopedias',
	    'AG'	=> 'Dictionaries and other general reference works',
	    'AI'	=> 'Indexes',
	    'AM'	=> 'Museums. Collectors and collecting',
	    'AN'	=> 'Newspapers',
	    'AP'	=> 'Periodicals',
	    'AS'	=> 'Academies and learned societies',
	    'AY'	=> 'Yearbooks. Almanacs. Directories',
	    'AZ'	=> 'History of scholarship and learning. The humanities',
	    'B'		=> 'Philosophy (General)',
	    'BC'	=> 'Logic',
	    'BD'	=> 'Speculative philosophy',
	    'BF'	=> 'Psychology',
	    'BH'	=> 'Aesthetics',
	    'BJ'	=> 'Ethics',
	    'BL'	=> 'Religions, Mythology, and Rationalism',
	    'BM'	=> 'Judaism',
	    'BP'	=> 'Islam, Bahaism, Theosophy, etc.',
	    'BQ'	=> 'Buddhism',
	    'BR'	=> 'Christianity, Bible',
	    'BS'	=> 'The Bible',
	    'BT'	=> 'Doctrinal Theology',
	    'BV'	=> 'Practical Theology',
	    'BX'	=> 'Christian Denominations',
	    'C'		=> 'Auxiliary Sciences of History',
	    'CB'	=> 'History of Civilization',
	    'D'		=> 'History (General)',
	    'DA'	=> 'History: Great Britain',
	    'DAW'	=> 'History: Central Europe',
	    'DB'	=> 'History: Autria, Liechtenstein, Hungary, Czechoslovakia',
	    'DC'	=> 'History: France, Andorra, Monaco',
	    'DD'	=> 'History: Germany',
	    'DE'	=> 'History: Greco-Roman World',
	    'DF'	=> 'History: Greece',
	    'DG'	=> 'History: Italy, Malta',
	    'DH'	=> 'History: Low Countries - Benelux Countries',
	    'DJ'	=> 'History: Netherlands',
	    'DJK'	=> 'History: Eastern Europe (General)',
	    'DS'	=> 'History: Asia',
	    'DT'	=> 'History: Africa',
	    'E'		=> 'History: America',
	    'F'		=> 'History: United States Regions',
	    'G'		=> 'Geography (General), Atlases, and Maps',
	    'GB'	=> 'Physical geography',
	    'GC'	=> 'Oceanography',
	    'GE'	=> 'Environmental Sciences',
	    'GF'	=> 'Human ecology and Anthropogeography',
	    'GN'	=> 'Anthropology',
	    'GR'	=> 'Folklore',
	    'GT'	=> 'Manners and customs',
	    'GV'	=> 'Recreation and Leisure',
	    'H'		=> 'Social Sciences',
	    'HA'	=> 'Statistics',
	    'HB'	=> 'Economic theory and demography',
	    'HC'	=> 'Economic history and conditions',
	    'HD'	=> 'Industries, Land use, and Labor',
	    'HE'	=> 'Transportation and communications',
	    'HF'	=> 'Commerce',
	    'HG'	=> 'Finance',
	    'HJ'	=> 'Public finance',
	    'HM'	=> 'Sociology (General)',
	    'HN'	=> 'Social history and conditions, Social problems, and Social reform',
	    'HQ'	=> 'The family, Marriage, and Women',
	    'HS'	=> 'Societies: secret, benevolent, etc.',
	    'HT'	=> 'Communities, Classes, and Races',
	    'HV'	=> 'Social pathology, Social and public welfare, and Criminology',
	    'HX'	=> 'Anarchism, Socialism, and Communism',
	    'J'		=> 'General legislative and executive papers',
	    'JA'	=> 'Political Science (General)',
	    'JC'	=> 'Political theory',
	    'JK'	=> 'Political institutions and public administration - United States',
	    'K'		=> 'Law',
	    'KBR'	=> 'History of Canon Law. Law of the Roman Catholic Church. The Holy See',
	    'KD'	=> 'Law of the United Kingdom and Ireland',
	    'KDZ'	=> 'Law of the Americas, Latin America, and the West Indies',
	    'KE'	=> 'Law of Canada',
	    'KF'	=> 'Law of the United States',
	    'KJ'	=> 'Law of Europe',
	    'KJV'	=> 'Law of France',
	    'KK'	=> 'Law of Germany',
	    'KL'	=> 'Law of Asia and Eurasia, Africa, Pacific Area and Antarctica',
	    'KZ'	=> 'Law of Nations',
	    'L'		=> 'Education',
	    'LA'	=> 'History of education',
	    'LB'	=> 'Theory and practice of education',
	    'LC'	=> 'Special aspects of ducation',
	    'LD'	=> 'Individual educational institutions in the U.S.',
	    'LE'	=> 'Individual educational institutions in the Americas',
	    'LF'	=> 'Individual educational institutions in Europe',
	    'LG'	=> 'Individual educational institutions "elsewhere"',
	    'LH'	=> 'College and school magazines and papers',
	    'LJ'	=> 'Student fraternities and societies, U.S.',
	    'LT'	=> 'Education textbooks',
	    'M'		=> 'Music',
	    'ML'	=> 'Literature on music',
	    'MT'	=> 'Music instruction and study',
	    'N'		=> 'Visual Arts',
	    'NA'	=> 'Architecture',
	    'NB'	=> 'Sculpture',
	    'NC'	=> 'Drawing. Design. Illustration',
	    'ND'	=> 'Painting',
	    'NE'	=> 'Print media',
	    'NK'	=> 'Decorative arts',
	    'NX'	=> 'Arts in general',
	    'P'		=> 'Philosophy and Linguistics',
	    'PA'	=> 'Greek and Latin language and literature',
	    'PB'	=> 'Modern and Celtic languages',
	    'PC'	=> 'Romanic languages',
	    'PD'	=> 'Germanic and Scandinavian languages',
	    'PE'	=> 'English language',
	    'PF'	=> 'West Germanic languages',
	    'PG'	=> 'Slavic, Baltic, and Albanian languages',
	    'PH'	=> 'Uralic and Basque languages',
	    'PJ'	=> 'Oriental languages and literatures',
	    'PK'	=> 'Indo-Iranian languages and literatures',
	    'PL'	=> 'Languages and literatures of Eastern Asia, Africa, and Oceania',
	    'PM'	=> 'Hyperborean, Indian, and Artificial languages',
	    'PN'	=> 'Literature (General)',
	    'PQ'	=> 'French, Italian, Spanish, and Portuguese literatures',
	    'PR'	=> 'English literature',
	    'PS'	=> 'American literature',
	    'PT'	=> 'German, Dutch, Scandinavian, etc. Literatures',
	    'PZ'	=> 'Fiction and juvenile belles lettres',
	    'Q'		=> 'Science (General)',
	    'QA'	=> 'Mathematics',
	    'QB'	=> 'Astronomy',
	    'QC'	=> 'Physics',
	    'QD'	=> 'Chemistry',
	    'QE'	=> 'Geology',
	    'QH'	=> 'Natural history and Biology',
	    'QK'	=> 'Botany',
	    'QL'	=> 'Zoology',
	    'QM'	=> 'Human anatomy',
	    'QP'	=> 'Physiology',
	    'QR'	=> 'Microbiology',
	    'R'		=> 'Medicine (General)',
	    'RA'	=> 'Public aspects of medicine',
	    'RB'	=> 'Pathology',
	    'RC'	=> 'Internal medicine',
	    'RD'	=> 'Surgery',
	    'RE'	=> 'Opthamology',
	    'RF'	=> 'Otorhinolaryngology',
	    'RG'	=> 'Gynecology and obstetrics',
	    'RJ'	=> 'Pediatrics',
	    'RK'	=> 'Dentistry',
	    'RL'	=> 'Dermatology',
	    'RM'	=> 'Therapeutics and pharmacology',
	    'RT'	=> 'Nursing',
	    'RV'	=> 'Botanic, Thomsonian, and eclectic medicine',
	    'RX'	=> 'Homeopathy',
	    'RZ'	=> 'Other systems of medicine',
	    'S'		=> 'Agriculture (General)',
	    'SB'	=> 'Plant culture',
	    'SD'	=> 'Forestry',
	    'SF'	=> 'Animal culture',
	    'SH'	=> 'Aquaculture, fisheries and angling',
	    'SK'	=> 'Hunting sports',
	    'T'		=> 'Technology (general)',
	    'TA'	=> 'Engineering (general and civil)',
	    'TC'	=> 'Hydraulic and ocean engineering',
	    'TD'	=> 'Environmental technology and sanitary engineering',
            'TE'        => 'Highway engineering, roads and pavements',
	    'TJ'	=> 'Mechanical engineering and machinery',
	    'TH'	=> 'Building contruction',
	    'TL'	=> 'Motor vehicles, astronautics and aeronautics',
	    'TK'	=> 'Electrical engineering. Electronics. Nuclear engineering',
	    'TR'	=> 'Photography',
	    'TS'	=> 'Manufactures',
	    'TT'	=> 'Handicrafts. Arts and crafts',
	    'TX'	=> 'Home economics',
	    'U'		=> 'Military Science. Naval Science',
	    'UB'	=> 'Military Administration',
	    'Z'		=> 'Books (General). Writing. Libraries. Bibliography',
	    'ZA'	=> 'Information resources (General)'
	);
    }
}

?>

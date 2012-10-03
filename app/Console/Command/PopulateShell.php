<?php
App::uses('Model', 'Model');
App::uses('Set', 'Uility');

/**
 * Populate Shell
 *
 * This is a simple utility to populate the database with data for testing, 
 * demoing, and other purposes.
 *
 */
class PopulateShell extends AppShell {

/**
 * Populate static data
 *
 * @var array
 */
	public $populate = array(
		'Collection' => array(
			array('Collection' => array(
				'display_name' => 'Peanut Butter Jelly Time',
				'display_description' => 'Time flies like an arrow; fruit flies like a banana -"Groucho" Marx',
				'created' => '1959-01-01 09:01',
				'modified' => '1959-01-01 09:01'
			)),
			array('Collection' => array(
				'display_name' => 'Lorem ipsum dolor sit amet',
				'display_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin auctor, lacus non elementum posuere, tortor dolor pellentesque ante, ac ultrices nulla orci ut turpis. Nulla congue, nunc ut pellentesque accumsan, massa arcu molestie nisi, eget sagittis lorem nibh id libero. Aenean interdum, lacus ultricies molestie convallis, tellus nulla suscipit orci, ut consequat magna mauris vitae magna. Nulla iaculis sapien volutpat nisl vehicula suscipit lacinia neque tincidunt. In sollicitudin risus in nunc dapibus ultricies. Nullam nec sapien mi, a dictum mi. Pellentesque tincidunt venenatis dui, vitae facilisis lectus consectetur vulputate. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Morbi at felis a dolor porta interdum non ac nisl.',
				'created' => '2010-01-01 12:30',
				'modified' => '2010-01-01 12:30'
			)),
			array('Collection' => array(
				'display_name' => 'Suspendisse mollis lobortis accumsan',
				'display_description' => 'Suspendisse mollis lobortis accumsan. Pellentesque sollicitudin, enim vitae vestibulum adipiscing, nulla sem ornare arcu, ac malesuada quam risus id enim. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam eu luctus ipsum. Aliquam sit amet faucibus turpis. Donec quis nibh justo. Aenean sed pellentesque augue. Pellentesque dictum rhoncus nisi, vitae cursus purus convallis id. Integer ultricies rhoncus adipiscing. Sed fermentum pellentesque urna, sed venenatis velit porttitor ut. Nunc a augue eu dolor auctor auctor.',
				'created' => '2010-01-02 13:13',
				'modified' => '2010-01-02 13:31'
			))
		),
		'Resource' => array(
			array('Resource' => array(
				'url' => 'http://www.jplayer.org/video/m4v/Incredibles_Teaser.m4v', // '/files/Incredibles_Teaser.m4v',
				'duration' => 97,
				'file_type' => 'video/mp4'
			)),
			array('Resource' => array(
				'url' => 'http://www.youtube.com/watch?v=0jTHNBKjMBU',
				'duration' => 427,
				'file_type' => 'video/youtube'
			)),
			array('Resource' => array(
				'url' => 'http://www.youtube.com/watch?v=-3y-6LDArp0',
				'duration' => 32,
				'file_type' => 'video/youtube'
			)),
			array('Resource' => array(
				'url' => 'http://www.youtube.com/watch?v=s8MDNFaGfT4',
				'duration' => 106,
				'file_type' => 'video/youtube'
			))
		),
		'Video' => array(
			array('Video' => array(
				'type' => 'video',
				'sort_order' => 1,
				'display_name' => 'The Incredibles Teaser',
				'display_description' => 'A family of undercover superheroes, while trying to live the quiet suburban life, are forced into action to save the world.',
				'resource_id' => 1,
				'collection_id' => 1
			)),
			array('Video' => array(
				'type' => 'video',
				'sort_order' => 2,
				'display_name' => 'Looney Tunes (Youtube)',
				'display_description' => 'Cartoons!',
				'resource_id' => 2,
				'collection_id' => 1
			)),
			array('Video' => array(
				'type' => 'video',
				'sort_order' => 3,
				'display_name' => 'Looney Tunes (Test Copy)',
				'display_description' => 'Cartoons (edit)!',
				'resource_id' => 2,
				'collection_id' => 1
			)),
			array('Video' => array(
				'type' => 'video',
				'sort_order' => 4,
				'display_name' => 'Finding Nemo Trailer ',
				'display_description' => 'Walt Disney Trailer: Finding Nemo',
				'resource_id' => 3,
				'collection_id' => 2
			)),
			array('Video' => array(
				'type' => 'video',
				'sort_order' => 5,
				'display_name' => 'Peanut Butter Jelly Time',
				'display_description' => "It's peanut butter jelly time!!!\nPeanut butter jelly time!!!\nPeanut butter jelly time!!!\n", 
				'resource_id' => 4,
				'collection_id' => 2
			))
		),
		'Note' => array(
			array('Note' => array(
				'type' => 'annotation',
				'user_id' => 1,
				'target_id' => 1,
				'parent_id' => null,
				'created' => '2012-05-01 13:04',
				'title' => 'Far Far Away Dummy Text',
				'body' => 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean.'
			)),
			array('Note' => array(
				'type' => 'annotation',
				'user_id' => 1,
				'target_id' => 1,
				'parent_id' => null,
				'created' => '2012-05-02 04:17',
				'title' => 'Cicero English Text',
				'body' => 'To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?'
			)),
			array('Note' => array(
				'type' => 'annotation',
				'user_id' => 1,
				'target_id' => 1,
				'parent_id' => null,
				'title' => 'Werther Text',
				'created' => '2012-05-01 12:51',
				'body' => 'A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents.'
			)),
			array('Note' => array(
				'type' => 'comment',
				'user_id' => 1,
				'target_id' => 1,
				'parent_id' => 1,
				'created' => '2012-04-01 19:23',
				'body' => 'The quick, brown fox jumps over a lazy dog. DJs flock by when MTV ax quiz prog. Junk MTV quiz graced by fox whelps. Bawds jog, flick quartz, vex nymphs. Waltz, bad nymph, for quick jigs vex!'
			)),
			array('Note' => array(
				'type' => 'comment',
				'user_id' => 1,
				'target_id' => 1,
				'parent_id' => 1,
				'created' => '2012-01-01 09:36',
				'body' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.'
			))
		),
		'Segment' => array(
			array('Segment' => array(
				'type' => 'time',
				'note_id' => 1,
				'start_time' => 10,
				'end_time' => 25
			)),	
			array('Segment' => array(
				'type' => 'time',
				'note_id' => 2,
				'start_time' => 26,
				'end_time' => 55, 
			)),
			array('Segment' => array(
				'type' => 'time',
				'note_id' => 3,
				'start_time' => 59,
				'end_time' => 73
			)),			
		)
	);

/**
 * Populate the application. 
 *
 * @return void
 */
	public function main() {
		$this->_clearData(array('Collection', 'Target', 'Resource', 'Note', 'Segment', 'User', 'UserCollection'));
		$this->_populateData();
		$this->out(__d('cake_console', 'Database populated'));
	}

/**
 * Returns a model object.
 *
 * @return object
 */
	protected function _m($model_name) {
		return ClassRegistry::init($model_name);
	}

/**
 * Resets the database.
 *
 * @return void
 */
	protected function _clearData($models) {
		foreach($models as $name) {
			$m = $this->_m($name);
			$m->deleteAll(array("1=1"), true, true);
			$m->getDataSource()->truncate($m->useTable);
		}
	}

/**
 * Inserts data into the database.
 *
 * @return void
 */
	protected function _populateData() {
		foreach($this->populate as $name => $records) {
			$model = $this->_m($name);
			foreach($records as $record) {
				$model->create();
				$result = $model->saveAll($record, array('validate' => 'first'));
				if($result === FALSE) {
					$this->out(__d('cake_console', "Model validation errors: ".var_export($model->validationErrors,1)));
				}
			}
			$this->out(__d('cake_console', "Populated model: $name"));
		}
		$this->_generateRandomUsers();
		$this->_generateRandomVideoNotes(15, true);
	}

/**
 * Inserts a random set of notes and comments with lorem ipsum text. 
 *
 * @param integer $count number of notes per target
 * @param boolean $withComments generate comments in addition to annotations 
 * @return void
 */
	protected function _generateRandomVideoNotes($count = 100, $enableComments = true, $pctWithComments = 0.8) {
		$video = $this->_m('Video');
		$note = $this->_m('Note');
		$segment = $this->_m('Segment');

		$targets = $video->find('all', array('recursive' => -1));
		$target_ids = Set::extract($targets, '{n}.Video.id');
		$num_users = count($this->users);

		foreach($target_ids as $target_id) {
			$this->out(__d('cake_console', "Generating random notes for video: $target_id"));

			for($i = 0; $i < $count; $i++) {
				$created = $this->_getRandomTimeLastWeek();
				$body = $this->_getLipsum(mt_rand(10,20));

				$note->create();
				$note->save(array(
					'type' => 'annotation',
					'target_id' => $target_id,
					'user_id' => $this->users[mt_rand(0, $num_users-1)],
					'title' => substr($body, 0, mt_rand(10, 30)) . '...',
					'body' => $body,
					'created' => date('Y-m-d H:i:s', $created)
				));

				$note_id = $note->id;

				$segment->create();
				$segment->save(array(
					'type' => 'time',
					'note_id' => $note_id,
					'start_time' => rand(0, 25),
					'end_time' => rand(26,60)
				));

				if($enableComments && (mt_rand(0,1) < $pctWithComments)) {
					$numComments = rand(1,8);
					for($j = 0; $j < $numComments; $j++) {
						$commentBody = $this->_getLipsum(mt_rand(5,20));
						$note->create();
						$note->save(array(
							'type' => 'comment',
							'target_id' => $target_id,
							'user_id' => $this->users[mt_rand(0, $num_users-1)],
							'parent_id' => $note_id,
							'body' => $commentBody,
							'created' => date('Y-m-d H:i:s', $created + mt_rand(0, 3600 * 24))
						));
					}
				}
			}
		}
	}


	/**
	 * Quick and dirty utility function to generate some lorem ipsum text. 
	 *
	 * @param integer $size percentage of lipsum text to use (0-100)
	 * @private
	 */
	protected function _getLipsum($size = 30) {
		$output = '';
		$endpos = ($size / 100) * strlen(self::$lipsumText);
		$section = substr(self::$lipsumText, 0, $endpos);
		$paragraphs = explode("\n\n", $section);

		foreach($paragraphs as $para) {
			$words = explode(" ", $para);
			$words_total = count($words);
			shuffle($words);

			// random sentences
			$slice_total = 0;
			while($slice_total < $words_total) {
				$slice_length = mt_rand(5,25);
				if($slice_total + $slice_length > $words_total) {
					$slice_length = $words_total - $slice_total;
				} 
				$slice_total += $slice_length;

				$sentence = array_splice($words, 0, $slice_length, array());
				$output .= ucfirst(trim(implode(' ', $sentence))) . '. ';
			}
			
			$output .= "\n\n";
		}

		return $output;
	}

/**
 * Utility function to get a random time one to seven days ago (i.e. in the
 * last week).
 * 
 * @return integer
 */
	protected function _getRandomTimeLastWeek() {
		$hour = 3600;
		$day = $hour * 24;
		$week = $day * 7;
		$now = time();

		return $now - mt_rand($day, $week); // 1 - 7 days ago 
	}

/**
 * Utility function to Pick a random name.
 *
 * @return string
 */
	protected function _generateRandomUserName() {
		// pick first name
		$gender = mt_rand(0,1);
		$candidates = self::$names[$gender];
		$num_candidates = count($candidates);
		$selection = mt_rand(0, $num_candidates-1);
		$firstname = $candidates[$selection];

		// always pick a masculine last name
		if($gender === 0) {
			// first name was male, so remove from the candidates
			unset($candidates[$selection]);
			$candidates = array_values($candidates); // re-index
			$num_candidates--;
		} else {
			// first name was female, so load masculine candidate names
			$candidates = self::$names[0];
			$num_candidates = count($candidates);
		}

		// pick last name
		$selection = mt_rand(0, $num_candidates-1);
		$lastname = $candidates[$selection];

		return "$firstname $lastname";
	}

/**
 * Inserts a random set of users
 *
 * @param integer $count number of users
 * @return void
 */
	protected function _generateRandomUsers($num_users = 100) {
		$this->out(__d('cake_console', "Generating random users"));

		$user = $this->_m('User');
		$userCollection = $this->_m('UserCollection');

		$collections = array_keys($this->_m('Collection')->find('list'));
		$num_collections = count($collections);

		$role_types = $this->_m('UserCollection')->getRoleTypes();
		$role_ids = Set::classicExtract($role_types, '{n}.Role.id');
		$num_roles = count($role_ids);

		for($user_idx = 0; $user_idx < $num_users; $user_idx++) {
			// create a user
			$name = $user_idx === 0 ? 'Root' : $this->_generateRandomUserName();
			$user->create(array(
				'name' => $name,
				'email' => strtolower(preg_replace('/[^a-zA-Z0-9._]/', '_', $name)) .'@localhost', 
				'created' => date('Y-m-d H:i:s', $this->_getRandomTimeLastWeek())
			));
			$user->save();


			// assign to a random number of collections with random roles
			if($user_idx === 0) {
				$collection_ids = $collections;
			} else {
				$offset = mt_rand(0, $num_collections-1);
				$length = mt_rand($offset, $num_collections);
				$collection_ids = array_slice($collections, $offset, $length);
			}
			
			foreach($collection_ids as $collection_id) {
				$role_id = $user_idx === 0 ? $role_ids[0] : $role_ids[mt_rand(0, $num_roles-1)];

				$userCollection->create();
				$userCollection->save(array(
					'UserCollection' => array(
						'user_id' => $user->id,
						'collection_id' => $collection_id,
						'role_id' => $role_id
				)));
			}

			$this->users[] = $user->id;
		}
	}

	public $users = array();

	public static $names = array(
		// men
		array('Adam', 'Ailwin', 'Alan', 'Alard', 'Aldred', 'Alexander', 'Alured', 'Amaury', 'Amalric', 'Anselm', 'Arnald', 'Asa', 'Aubrey', 'Baldric', 'Baldwin', 'Bartholomew', 'Bennet', 'Bertram', 'Blacwin', 'Colin', 'Constantine', 'David', 'Edwin', 'Elias', 'Helyas', 'Engeram', 'Ernald', 'Eustace', 'Fabian', 'Fordwin', 'Forwin', 'Fulk', 'Gamel', 'Geoffrey', 'Gerard', 'Gervase', 'Gilbert', 'Giles', 'Gladwin', 'Godwin', 'Guy', 'Hamo', 'Hamond', 'Harding', 'Henry', 'Herlewin', 'Hervey', 'Hugh', 'James', 'Jocelin', 'John', 'Jordan', 'Lawrence', 'Leofwin', 'Luke', 'Martin', 'Masci', 'Matthew', 'Maurice', 'Michael', 'Nigel', 'Odo', 'Oliva', 'Osbert', 'Norman', 'Nicholas', 'Peter', 'Philip', 'Ralf/Ralph', 'Ranulf', 'Richard', 'Robert', 'Roger', 'Saer', 'Samer', 'Savaric', 'Silvester', 'Simon', 'Stephan', 'Terric', 'Terry/Thierry', 'Theobald', 'Thomas', 'Thurstan', 'Umfrey', 'Waleran', 'Walter', 'Warin', 'William', 'Wimarc', 'Ymbert'),
		// women
		array('Ada', 'Adelina', 'Agnes', 'Albreda', 'Aldith', 'Aldusa', 'Alice', 'Alina', 'Amanda', 'Amice', 'Amicia', 'Amiria', 'Anabel', 'Annora', 'Ascilia', 'Avelina', 'Avoca', 'Avice', 'Beatrice', 'Basilea', 'Bela', 'Berta', 'Celestria', 'Christiana', 'Cicely', 'Clarice', 'Constance', 'Dionisia', 'Denise', 'Edith', 'Ellen', 'Eleanor', 'Elizabeth', 'Emma', 'Estrilda', 'Isabel', 'Eva', 'Felicia', 'Fina', 'Goda', 'Golda', 'Grecia', 'Gundrea', 'Gundred', 'Gunnora', 'Haunild', 'Hawisa', 'Helen', 'Elena', 'Helewise', 'Hilda', 'Ida', 'Idonea', 'Isolda', 'Joanna', 'Julian', 'Katherine', 'Leticia', 'Lettice', 'Liecia', 'Linota', 'Lora', 'Lucia Mabel', 'Malota', 'Margaret', 'Margery', 'Marsilia', 'Mary', 'Matilda', 'Maud', 'Mazelina', 'Millicent', 'Muriel', 'Nesta', 'Nicola', 'Philippa', 'Parnel', 'Petronilla', 'Primeveire', 'Richenda', 'Richolda', 'Roesia', 'Sabina', 'Sabelina', 'Sarah', 'Susanna', 'Sybil')
	);

	public static $lipsumText = <<<__END_LIPSUM
lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet

duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi lorem ipsum dolor sit amet consectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat

ut wisi enim ad minim veniam quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi

nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum lorem ipsum dolor sit amet consectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat ut wisi enim ad minim veniam quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat

duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat vel illum dolore eu feugiat nulla facilisis

at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr at accusam aliquyam diam diam dolore dolores duo eirmod eos erat et nonumy sed tempor et et invidunt justo labore stet clita ea et gubergren kasd magna no rebum sanctus sea sed takimata ut vero voluptua est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat

consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua at vero eos et accusam et justo duo dolores et ea rebum stet clita kasd gubergren no sea takimata sanctus est lorem ipsum dolor sit amet
__END_LIPSUM;
}

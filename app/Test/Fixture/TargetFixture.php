<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * TargetFixture
 *
 */
class TargetFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'Target';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'type' => 'image',
			'collection_id' => 1,
			'resource_id' => 1,
			'sort_order' => 1,
			'display_name' => 'Lorem ipsum dolor sit amet',
			'display_description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'display_creator' => 'Authorus Brilliantus',
			'hidden' => 0,
			'deleted' => 0
		),
		array(
			'id' => 2,
			'type' => 'sound',
			'collection_id' => 1,
			'resource_id' => 1,
			'sort_order' => 2,
			'display_name' => 'Test Testing Tester Tested Testable',
			'display_description' => 'Supercalifragilisticexpialidocious!',
			'display_creator' => 'Super Dooper Creator',
			'hidden' => 0,
			'deleted' => 0
		),
		array(
			'id' => 3,
			'type' => 'video',
			'collection_id' => 1,
			'resource_id' => 2,
			'sort_order' => 3,
			'display_name' => 'The sundry leaps next to a difficult knowledge.',
			'display_description' => 'Pellentesque sit amet augue quis enim ornare semper ac sed elit?',
			'display_creator' => 'Syndrig',
			'hidden' => 0,
			'deleted' => 0
		),
		array(
			'id' => 4,
			'type' => 'text',
			'collection_id' => 1,
			'resource_id' => 3,
			'sort_order' => 4,
			'display_name' => 'A plastic beach starves against the propaganda.',
			'display_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae neque augue, sed malesuada dui. Sed at justo felis. Phasellus eget felis et massa vehicula.',
			'display_creator' => 'Who?',
			'hidden' => 1,
			'deleted' => 1
		),
		array(
			'id' => 5,
			'type' => 'video',
			'collection_id' => 1,
			'resource_id' => 3,
			'sort_order' => 5,
			'display_name' => 'This is the name',
			'display_description' => 'This is the description',
			'display_creator' => 'The creator',
			'hidden' => 1,
			'deleted' => 0
		),
		array(
			'id' => 6,
			'type' => 'video',
			'collection_id' => 1,
			'resource_id' => 3,
			'sort_order' => 5,
			'display_name' => 'This is the name again',
			'display_description' => 'This is the description for you',
			'display_creator' => 'The creator is who?',
			'hidden' => 0,
			'deleted' => 0
		)
	);
}

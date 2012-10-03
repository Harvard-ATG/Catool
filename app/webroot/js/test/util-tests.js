(function() {
	var TimeConverter = Catool.utils.TimeConverter;

	module("utils.TimeConverter");

	test("instance creation", function() {
		expect(2);

		var t = new TimeConverter();
		ok(t instanceof TimeConverter, 'classic instance creation');
		ok(TimeConverter() instanceof TimeConverter, 'functional instance creation');
	});

	test("validity of numeric time values", function() {
		ok(!TimeConverter(-1).isValid(), "-1 should be invalid");
		ok(TimeConverter(0).isValid(), "0 should be valid");
		ok(TimeConverter(1).isValid(), "1 should be valid");
		ok(TimeConverter(9999).isValid(), "9999 should be valid");
	});

	test("validity of string time values", function() {
		ok(TimeConverter("00:00:00.1").isValid());
		ok(TimeConverter("00:00:00,1").isValid());

		ok(TimeConverter("12:34:56.7").isValid());
		ok(TimeConverter("2:34:56.7").isValid());
		ok(TimeConverter("34:56.7").isValid());
		ok(TimeConverter("4:56").isValid());
		ok(TimeConverter("0:00").isValid());

		ok(!TimeConverter(":56").isValid());
		ok(!TimeConverter("0:5").isValid());
		ok(!TimeConverter("0a:1b:2c").isValid());
		ok(!TimeConverter("ab:cd").isValid());
		ok(!TimeConverter("x:yz").isValid());
		ok(!TimeConverter(null).isValid());
		ok(!TimeConverter().isValid());
	});

	test("conversion to seconds", function() {
		equal(TimeConverter("00:12:34").toSeconds(), 754);
		equal(TimeConverter("0:12:34").toSeconds(), 754);
		equal(TimeConverter("12:34").toSeconds(), 754);

		equal(TimeConverter("00:00").toSeconds(), 0);
		equal(TimeConverter("0:00").toSeconds(), 0);
		equal(TimeConverter("00:59").toSeconds(), 59);
		equal(TimeConverter("01:00").toSeconds(), 60);
		equal(TimeConverter("01:01").toSeconds(), 61);
		equal(TimeConverter("01:30").toSeconds(), 90);
		equal(TimeConverter("1:00:00").toSeconds(), 3600);
		equal(TimeConverter("01:00:00").toSeconds(), 3600);
		equal(TimeConverter("1:01:01").toSeconds(), 3661);
		equal(TimeConverter("01:01:01").toSeconds(), 3661);
	});

	test("conversion to string", function() {
		equal(TimeConverter("00:12:34").toString(), "0:12:34");
		equal(TimeConverter("0:12:34").toString(), "0:12:34");
		equal(TimeConverter("12:34").toString(), "0:12:34");
		equal(TimeConverter("0:34").toString(), "0:00:34");

		equal(TimeConverter(59).toString(), "0:00:59");
		equal(TimeConverter(60).toString(), "0:01:00");
		equal(TimeConverter(61).toString(), "0:01:01");
		equal(TimeConverter(3600).toString(), "1:00:00");
		equal(TimeConverter(3661).toString(), "1:01:01");
	});

})();

(function() {
	var NoteSyncModel = Catool.utils.NoteSyncModel; 
	var Notes = Catool.collections.Notes

	module("utils.NoteSyncModel", {
		setup: function() {
			this.syncModel = new NoteSyncModel({
				player: null
			});
			ok(this.syncModel instanceof NoteSyncModel, 'should be a note sync model');
		}, 
		teardown: function() {
		}
	});
})();

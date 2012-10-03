#!/usr/bin/env perl
# Copy me to the .git/hooks directory and rename file to "pre-commit"
#
# This script is invoked by "git commit" and runs some PHP and JS syntax checks
# on the files staged for commit. It exits with a non-zero status if any check
# fails.

use strict;
use warnings;

exit run_hooks(\&check_php_syntax, \&check_js_syntax);

sub run_hooks {
	my @hooks = @_;

	# Get a list of files staged for commit
	my @staged_files = staged_files();

	# Stash unstaged changes. That is, set the working copy
	# to the same state as HEAD, but keep any changes that have been
	# staged for commit.
	run_command('git', 'stash', 'save', '--quiet', '--keep-index');

	# Execute each hook on the changes
	my $failed = 0;
	foreach my $hook (@hooks) {
		$failed = $hook->(@staged_files) || $failed;
	}

	# Restore the stashed changes to the working copy
	run_command('git', 'stash', 'pop', '--quiet');

	print "\nAborting commit due to pre-commit hook errors...\n" if $failed;

	return $failed;
}

sub run_command {
	system(@_) == 0 or die "system @_ failed: $?";
}

sub staged_files {
	my $output = qx{git diff --cached --name-status};
	my @lines = split("\n", $output);
	my @staged_files;

	# parse diff to extract status and file name
	foreach my $line (@lines) {
		my ($status, $name) = split /\s+/, $line, 2;
		$name =~ s/(?:^")|(?:"$)//g; # extract name from quotes
		next if $status eq 'D'; # skip deleted items
		push @staged_files, $name;
	}

	return @staged_files;
}

sub check_php_syntax {
	my @files = grep /(?:\.php)|(?:\.ctp)$/, @_;
	my $failed = 0;

	local $/ = undef; # enable slurp mode 

	foreach my $file (@files) {
		my $output = qx{php -l $file};
		my $error = ($output =~ /Parse error/);
		$failed = $failed || $error;

		print $output if $error;
	}

	print "\n" if $failed;

	return $failed;
}

sub check_js_syntax {
	my @files = grep /\.js/, @_;
	my $failed = 0;

	local $/ = undef; # enable slurp mode

	foreach my $file (@files) {
		my $output = qx{jsl -nologo -nofilelisting -nosummary -nocontext -process $file};
		my $code = ($? >> 8);
		my $error = ($code >= 3);
		$failed = $failed || $error;

		print $output if $error;
	}

	return $failed;
}


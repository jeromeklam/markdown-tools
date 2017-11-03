<?php
# Create an executable phar
# Name of our archive.
$phar = new Phar("dist/markdown-tools.phar");
# Have to do buffering to make things executable.
# See http://stackoverflow.com/questions/11082337/how-to-make-an-executable-phar
$phar->startBuffering();
# Default executable.
$defaultStub = $phar->createDefaultStub('app/markdown_tools.php');
# Build from the project directory. Assumes that createPhar.php (this file) is in the project root.
$phar->buildFromDirectory(dirname(__FILE__));
# Add the header to enable execution.
$stub = "#!/usr/bin/env php \n" . $defaultStub;
# Set the stub.
$phar->setStub($stub);
# Wrap up.
$phar->stopBuffering();
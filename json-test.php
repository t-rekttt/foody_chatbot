<?php
if (isset($_GET['data'])) {
	header('Content-Type: application/json');
	echo $_GET['data'];
}
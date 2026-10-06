<?php

use CrediSoporte\Domain\Models\Proposal;
use CrediSoporte\Domain\Request\Request;

require_once '../vendor/autoload.php';
require_once '../src/Domain/Database/bootstrap.php';

$request = new Request();

Proposal::where('id', $request->get('proposalId'))->delete();

header('Location: ./propuestas.php');
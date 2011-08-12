<?php
// Copyright (c) 2011 Cristian Adamo.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.

// set a target server WITHOUT 'http://' and the trailing slash.
// You needs to set ap a wildcard dns record, to guaratee that we will resolve
// an unique URL to  get a non-cached content.
// By the other hand we'll be able to measure the DNS resolve time
return array("xyz.example.com", "http://doppler.example.com");

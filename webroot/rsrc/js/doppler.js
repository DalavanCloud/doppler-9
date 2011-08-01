// Copyright (c) 2011 Cristian Adamo. All rights reserved.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.

CX.log('About to install Doppler.');

CX.provide('Doppler', {
  _doppler_transport : null,
  _method : null,
  _url : null,
  _host : null,
  _origin : null,
  _user_host : null,
  _callback : null,
  _test : null,
  _stats: {},
  _response: {},

  construct: function(method, url, callback) {
    if (!url) {
      return;
    }
    this._url = url;
    this._method = method || 'GET';
    this._callback = callback;
    CX.log('Doppler: constructed! [DONE]');
  },

  setOptions: function(test, origin, user_host) {
    this._test = test || '__IMAGE__';
    this._origin = origin;
    this._user_host = user_host || 'none';
    CX.log('Doppler: setOptions [DONE]');
  },

  execute: function() {
    this._generateUniqueHostName();
    this._runTest();
    // Start DNS lookup test.
    this._doRequest(_onDNSComplete);
    CX.log('Doppler: run [DONE]');
  },

  _onDopplerComplete: function() {
    this._stats['host'] = this._host;
    this._callback(this._stats, this._response);
    CX.log('Doppler: _onDopplerComplete [DONE]');
  },

  _onDNSComplete: function() {
    if ((this._doppler_transport.readyState == 4) &&
        (this._doppler_transport.status == 200)) {
      this._stats['dns'] = id(new Date()).getTime() - this._stats['start'];
      this._response['dns'] = this._doppler_transport.responseText;
      // start HTTP test
      this._doRequest(_onHTTPComplete);
    }
  },

  _onHTTPComplete: function() {
    if ((this._doppler_transport.readyState == 4) &&
        (this._doppler_transport.status == 200)) {
      this._stats['http'] = id(new Date()).getTime() - this._stats['start'];
      this._response['http'] = this._doppler_transport.responseText;

      this._onDopplerComplete();
    }
  },

  _doRequest: function(callback) {
    if (this._doppler_transport) {
      this._doppler_transport.abort();
      this._doppler_transport = null;
    }

    /**
     * We need to minimize the footprint since this is highly critical to the
     * performance of the test.
     */
    try {
      try {
        this._doppler_transport = new XMLHttpRequest();
      } catch (x) {
        this._doppler_transport = new ActiveXObject("Msxml2.XMLHTTP");
      }
    } catch (x) {
      this._doppler_transport = new ActiveXObject("Microsoft.XMLHTTP");
    }

    this._doppler_transport.open(this._method, this._host);
    this._doppler_transport.onreadystatechange = CX.bind(this, this.callback)

    this._stats['start'] = id(new Date()).getTime();
    this._doppler_transport.send();
  },

  _generateUniqueHostName: function() {
    var rand = Math.floor(Math.random()*(445588771122)).toString(32),
        unique_host = this._generateUniqueHostName(),
        clear_host = this._origin.replace(/[.]/g,"");

    this._host = 'http://' + clear_host + '-' + unique_host + '.' + this._url;

    if (this._test == '__IMAGE__') {
      this._host = this._host + '?test=image&';
    } else {
      this._host = this._host + '?test=lorem&';
    }

    this._host = this._host + this._user_host;
  }
});

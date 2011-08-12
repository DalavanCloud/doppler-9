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
    this._url = url;
    this._method = method || 'GET';
    this._callback = callback;
    return this;
  },

  setOptions: function(test, origin, user_host) {
    this._test = test || '__IMAGE__';
    this._origin = origin;
    this._user_host = user_host || null;
    return this;
  },

  execute: function() {
    this._generateUniqueHostName();
    this._doRequest(this._onDNSComplete);
    CX.log('Doppler: run [DONE]');
  },

  _onDopplerComplete: function() {
    this._stats['host'] = this._host;
    this._callback(this._stats, this._response);
  },

  _onDNSComplete: function() {
    if ((this._doppler_transport.readyState == 4) &&
        (this._doppler_transport.status == 200)) {
      this._stats['dns'] = CX.id(new Date()).getTime() - this._stats['start'];
      this._response['dns'] = this._doppler_transport.responseText;
      // start HTTP test
      this._doRequest(this._onHTTPComplete);
    }
  },

  _onHTTPComplete: function() {
    if ((this._doppler_transport.readyState == 4) &&
        (this._doppler_transport.status == 200)) {
      this._stats['http'] = CX.id(new Date()).getTime() - this._stats['start'];
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
    this._doppler_transport.onreadystatechange = CX.bind(this, callback)

    this._stats['start'] = CX.id(new Date()).getTime();
    this._doppler_transport.send();
  },

  _generateUniqueHostName: function() {
    var unique_host = Math.floor(Math.random()*(445588771122)).toString(32),
        clear_host = this._origin.replace(/[.]/g,"");

    this._host = 'http://' + clear_host + '-' + unique_host + '.' + this._url;

    if (this._test == '__IMAGE__') {
      this._host = this._host + '?test=image&';
    } else {
      this._host = this._host + '?test=lorem&';
    }

    this._host = this._host + 'origin=' + this._user_host;
  }
});

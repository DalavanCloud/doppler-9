
JG.provide('Doppler', {
  _doppler_transport : null,
  _method : null,
  _url : null,
  _host: null,
  _callback : null,
  _stats: {},
  _test: null,

  init: function(method, url, callback) {
    if (!url) {
      return;
    }
    this._url = url;
    this._method = method || 'GET';
    this._callback = callback;
  },

  setTest: function(test) {
    this._test = test || '__IMAGE__';
  }

  run: function() {
    if (!this._test)
      return;
    }

    var unique_host = this._generateUniqeHostName();
    this._host = 'http://' + unique_host + this._url;


  },

  onDopplerComplete: function() {
    this._stats['dns'] = this._stats.dns - this._stats.http;
    this._callback(this._stats)
  },

  _runDNSTest: function() {
    this._doRequest('__dns__', _onDNSComplete);
  },

  _runHTTPTest: function() {
    this._doRequest('__http__', _onHTTPComplete);
  },

  _onDNSComplete: function() {
    if ((this._doppler_transport.readyState == 4) &&
        (_doppler_transport.status == 200)) {
      this._stats['dns'] = id(new Date()).getTime() - this._stats['start'];
    }
  },

  _onHTTPComplete: function() {
    if ((this._doppler_transport.readyState == 4) &&
        (_doppler_transport.status == 200)) {
      this._stats['http'] = id(new Date()).getTime() - this._stats['start'];
    }
  },

  _doRequest: function(type, callback) {
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

    this._doppler_transport.open(this._method, host);
    this._doppler_transport.onreadystatechange = JG.bind(this, this.callback)

    this._stats['start'] = id(new Date()).getTime();
    this._doppler_transport.send();
  }

  _generateUniqeHostName: function() {
    return Math.floor(Math.random()*(445588771122)).toString(32);
  }





   var start = 0, end = 0, duration = 0,
     c = document.getElementById('doppler-content');
     s = document.getElementById('doppler-status');
     e = document.getElementById('doppler-epoch');

       s.innerHTML = '--- Doppler Completed! ---';
       s.style.background = '#68A64D';
       doppler_response = JSON.parse(doppler.responseText);
       duration = end-start;

       e.innerHTML =
         "<h3>Information:</h3> <br>" +
         "<strong>Target site:</strong>    " + "{$target_domain}" + "<br><br>" +
         "<strong>NETWORK LATENCY:</strong>  " +
           (duration-(doppler_response.epoch/1000)) + " ms<br>" +
         "<strong>Request Duration:</strong>   " +
           duration + " ms " + "<br>" +
         "<strong>Server Processing Time:</strong>   " +
           doppler_response.epoch + " us<br><br>" +
         "<strong>DOWNLOAD TIMESTAMP:</strong><br>" +
           "Started at:         " + start + "<br>" +
           "Completed at:    " + end;

       c.innerHTML = '<strong>RESPONSE TO:</strong>  <i>' +
                     doppler_response.title + '</i><br><br>' +
                     doppler_response.content;
     }
   };

});

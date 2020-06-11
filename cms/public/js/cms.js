
cms = {
    ///////////////// CONFIG //////////////////////
    
    url: {
        base: '/',
    },
    
    zend: {
        plugin: 'index',
        controller: 'index',
        action: 'index'
    },
    
    framework: {
        bootstrap: 3,
    },
    
    ///////////////// MAIN ////////////////////////
    checkPluginExists: function(){
        
    },
    
    ///////////////// HTTP //////////////////////
    http: {
        post: function(url,data,success,error,fail) {
            
            if (typeof url=='object') {
                data = url.params;
                success = url.success;
                error = url.error;
                fail = url.fail;
                url = url.url;
            }

            if (!fail) {
                failFunc = function(){
                    cms.info.show('Ошибка :(');
                };
            }else failFunc = fail;

            return $.post(url,data,function(ret) {
                    if (ret['error']!='') {

                        if (error!==undefined) {
                            error(ret);
                        }else cms.info.show(ret['error']);

                        return false;

                    }else {
                            if (success!==undefined)
                                success(ret);
                            else
                                document.location.reload();
                            return true;
                    }
            },'json').fail(failFunc);            
        },
        
        postWithFiles: function(url, elementId, success, fail) {
            return sendPostAjax(url, elementId, success, fail)
        }
    },
    //////////////////////////////////////////////
    tools: {
        
        string: {
            translit: function(str){
                
                var ru = "а,б,в,г,д,е,ё,ж,з,и,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ы,э,ю,я".split(",");
                var en = "a,b,v,g,d,e,e,zh,z,i,k,l,m,n,o,p,r,s,t,u,f,h,c,ch,sh,sch,iy,e,yu,ya".split(",");
                str = str.toLowerCase();
                
                for (var x=0;x<ru.length; x++) {
                    var re = new RegExp(ru[x],"g");
                    
                    str = str.replace(re,en[x]);
                }
                
                str = str.replace(/[^a-z0-9_\s-]/g,'');
                
                str = str.replace(/^\s+|\s+$/g,'');
                
                str = str.replace(/ /g,'_');
                
                return str;
            },
    
            randString: function(len) {
                if (typeof len==='undefined')
                    len = 32;

                var number = Math.floor(Math.random() * (999999 - 1 + 1)) + 1;

                var str = cms.tools.Sha1.hash(Date.now()+'RANDOM'+number);

                return str.substr(0, len);
                
            }
        },
        
        randString: function(len){
            return cms.tools.string.randString(len);
        },
        
        rand: function(min, max) {
            return Math.floor(Math.random() * (max - min)) + min;
        },
        
        
        
        Sha1: {
            /**
             * Generates SHA-1 hash of string.
             *
             * @param   {string} msg - (Unicode) string to be hashed.
             * @returns {string} Hash of msg as hex character string.
             */
            hash: function(msg) {
                // convert string to UTF-8, as SHA only deals with byte-streams
                msg = msg.utf8Encode();

                // constants [§4.2.1]
                var K = [ 0x5a827999, 0x6ed9eba1, 0x8f1bbcdc, 0xca62c1d6 ];

                // PREPROCESSING

                msg += String.fromCharCode(0x80);  // add trailing '1' bit (+ 0's padding) to string [§5.1.1]

                // convert string msg into 512-bit/16-integer blocks arrays of ints [§5.2.1]
                var l = msg.length/4 + 2; // length (in 32-bit integers) of msg + ‘1’ + appended length
                var N = Math.ceil(l/16);  // number of 16-integer-blocks required to hold 'l' ints
                var M = new Array(N);

                for (var i=0; i<N; i++) {
                    M[i] = new Array(16);
                    for (var j=0; j<16; j++) {  // encode 4 chars per integer, big-endian encoding
                        M[i][j] = (msg.charCodeAt(i*64+j*4)<<24) | (msg.charCodeAt(i*64+j*4+1)<<16) |
                            (msg.charCodeAt(i*64+j*4+2)<<8) | (msg.charCodeAt(i*64+j*4+3));
                    } // note running off the end of msg is ok 'cos bitwise ops on NaN return 0
                }
                // add length (in bits) into final pair of 32-bit integers (big-endian) [§5.1.1]
                // note: most significant word would be (len-1)*8 >>> 32, but since JS converts
                // bitwise-op args to 32 bits, we need to simulate this by arithmetic operators
                M[N-1][14] = ((msg.length-1)*8) / Math.pow(2, 32); M[N-1][14] = Math.floor(M[N-1][14]);
                M[N-1][15] = ((msg.length-1)*8) & 0xffffffff;

                // set initial hash value [§5.3.1]
                var H0 = 0x67452301;
                var H1 = 0xefcdab89;
                var H2 = 0x98badcfe;
                var H3 = 0x10325476;
                var H4 = 0xc3d2e1f0;

                // HASH COMPUTATION [§6.1.2]

                var W = new Array(80); var a, b, c, d, e;
                for (var i=0; i<N; i++) {

                    // 1 - prepare message schedule 'W'
                    for (var t=0;  t<16; t++) W[t] = M[i][t];
                    for (var t=16; t<80; t++) W[t] = cms.tools.Sha1.ROTL(W[t-3] ^ W[t-8] ^ W[t-14] ^ W[t-16], 1);

                    // 2 - initialise five working variables a, b, c, d, e with previous hash value
                    a = H0; b = H1; c = H2; d = H3; e = H4;

                    // 3 - main loop
                    for (var t=0; t<80; t++) {
                        var s = Math.floor(t/20); // seq for blocks of 'f' functions and 'K' constants
                        var T = (cms.tools.Sha1.ROTL(a,5) + cms.tools.Sha1.f(s,b,c,d) + e + K[s] + W[t]) & 0xffffffff;
                        e = d;
                        d = c;
                        c = cms.tools.Sha1.ROTL(b, 30);
                        b = a;
                        a = T;
                    }

                    // 4 - compute the new intermediate hash value (note 'addition modulo 2^32')
                    H0 = (H0+a) & 0xffffffff;
                    H1 = (H1+b) & 0xffffffff;
                    H2 = (H2+c) & 0xffffffff;
                    H3 = (H3+d) & 0xffffffff;
                    H4 = (H4+e) & 0xffffffff;
                }

                return cms.tools.Sha1.toHexStr(H0) +
                       cms.tools.Sha1.toHexStr(H1) + 
                       cms.tools.Sha1.toHexStr(H2) +
                       cms.tools.Sha1.toHexStr(H3) + 
                       cms.tools.Sha1.toHexStr(H4);
            },


            /**
             * Function 'f' [§4.1.1].
             * @private
             */
            f: function(s, x, y, z)  {
                switch (s) {
                    case 0: return (x & y) ^ (~x & z);           // Ch()
                    case 1: return  x ^ y  ^  z;                 // Parity()
                    case 2: return (x & y) ^ (x & z) ^ (y & z);  // Maj()
                    case 3: return  x ^ y  ^  z;                 // Parity()
                }
            },

            /**
             * Rotates left (circular left shift) value x by n positions [§3.2.5].
             * @private
             */
            ROTL: function(x, n) {
                return (x<<n) | (x>>>(32-n));
            },


            /**
             * Hexadecimal representation of a number.
             * @private
             */
            toHexStr: function(n) {
                // note can't use toString(16) as it is implementation-dependant,
                // and in IE returns signed numbers when used on full words
                var s="", v;
                for (var i=7; i>=0; i--) { v = (n>>>(i*4)) & 0xf; s += v.toString(16); }
                return s;
            }

        }
    },
    
    info: {
        show: function(text, delay) {
            
            var html = "<div class='info_toolbox'>"+text+"</div>";
            
            if (!delay)
                delay = 1800;
            
            if ($('.info_toolbox').length==0)
                $('body').append(html);
            
            $('.info_toolbox').css("top",-300).fadeIn(10,function(){
                $('.info_toolbox').css('margin-left',($('.info_toolbox').width()/2*-1)).html(text);
            });
            
            
            $('.info_toolbox').animate({
                top: "10%",
            },333,function(){
                
                setTimeout(function(){
                    $('.info_toolbox').fadeOut(666,function(){
                        $(this).remove();
                    })
                },delay);
            })
        },
        
        hide: function(delay) {
            $('.info_toolbox').fadeOut(delay);
        }
    },
    
    dialog: {
        
        id: null,
        dids: [],
        /*
         * @desc params is
         * url
         * title
         * buttons
         * width
         * data,
         * callback_after_load,
         * callback_after_close
         */
        show: function(urlOrParams, title, buttons, width, data, callback_after_load) {
            
            var callback_after_close = null;
            var url = null;
            
            if (typeof urlOrParams=='object') {
                url = urlOrParams.url;
                title = urlOrParams.title;
                buttons = urlOrParams.buttons;
                width = urlOrParams.width;
                
                data = urlOrParams.data || urlOrParams.params;
                
                callback_after_load = urlOrParams.callback_after_load;
                callback_after_close = urlOrParams.callback_after_close;
            }else {
                url = urlOrParams;
            }
            
            try {
                if (cms.framework.bootstrap==4) {
                    cms.dialog.id = bsAjaxDialog4(url, title, buttons, width, data, callback_after_load, callback_after_close);
                }else
                    cms.dialog.id = bsAjaxDialog3(url, title, buttons, width, data, callback_after_load, callback_after_close);
            }catch (e) {
                cms.dialog.id = bsAjaxDialog3(url, title, buttons, width, data, callback_after_load, callback_after_close);
            }
            
            
            return cms.dialog.id;
        },
        
        hide: function(did){
            bsDialogDestroy(did);
        }
    },
    
    settings: {
        save: function(){
            var data = $('#configphp').serializeArray();
            
            cms.http.post('/skineditor/settings/save',data,function(){
                window.parent.cms.info.show('Сохранено!');
            });
        }
    },
    
    site: {
        settings: {
            save: function(name, val){
                cms.http.post('/admin/settings/save',{
                    name: name,
                    value: val
                },function(){
                    cms.info.show('Сохранено');
                });
            }
        }
    },
    
    debug: {
        report: function(e) {
            //@TODO
            ///отправка отчёта об ошибке админу на мыло
        }
    }
}


/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

/** Extend String object with method to encode multi-byte string to utf8
 *  - monsur.hossa.in/2012/07/20/utf-8-in-javascript.html */
if (typeof String.prototype.utf8Encode == 'undefined') {
    String.prototype.utf8Encode = function() {
        return unescape( encodeURIComponent( this ) );
    };
}

/** Extend String object with method to decode utf8 string to multi-byte */
if (typeof String.prototype.utf8Decode == 'undefined') {
    String.prototype.utf8Decode = function() {
        try {
            return decodeURIComponent( escape( this ) );
        } catch (e) {
            return this; // invalid UTF-8? return as-is
        }
    };
}

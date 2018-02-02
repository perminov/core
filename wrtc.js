'use strict';
var WRCall;

function md5(_0x18d8x3) {
    var _0x18d8x4;
    var _0x18d8x5 = function(_0x18d8x6, _0x18d8x7) {
        return (_0x18d8x6 << _0x18d8x7) | (_0x18d8x6 >>> (32 - _0x18d8x7))
    };
    var _0x18d8x8 = function(_0x18d8x9, _0x18d8xa) {
        var _0x18d8xb, _0x18d8xc, _0x18d8xd, _0x18d8xe, _0x18d8xf;
        _0x18d8xd = (_0x18d8x9 & 0x80000000);
        _0x18d8xe = (_0x18d8xa & 0x80000000);
        _0x18d8xb = (_0x18d8x9 & 0x40000000);
        _0x18d8xc = (_0x18d8xa & 0x40000000);
        _0x18d8xf = (_0x18d8x9 & 0x3FFFFFFF) + (_0x18d8xa & 0x3FFFFFFF);
        if (_0x18d8xb && _0x18d8xc) {
            return (_0x18d8xf ^ 0x80000000 ^ _0x18d8xd ^ _0x18d8xe)
        };
        if (_0x18d8xb || _0x18d8xc) {
            if (_0x18d8xf && 0x40000000) {
                return (_0x18d8xf ^ 0xC0000000 ^ _0x18d8xd ^ _0x18d8xe)
            } else {
                return (_0x18d8xf ^ 0x40000000 ^ _0x18d8xd ^ _0x18d8xe)
            }
        } else {
            return (_0x18d8xf ^ _0x18d8xd ^ _0x18d8xe)
        }
    };
    var _0x18d8x10 = function(_0x18d8x11, _0x18d8x12, _0x18d8x13) {
        return (_0x18d8x11 & _0x18d8x12) | ((~_0x18d8x11) & _0x18d8x13)
    };
    var _0x18d8x14 = function(_0x18d8x11, _0x18d8x12, _0x18d8x13) {
        return (_0x18d8x11 & _0x18d8x13) | (_0x18d8x12 & (~_0x18d8x13))
    };
    var _0x18d8x15 = function(_0x18d8x11, _0x18d8x12, _0x18d8x13) {
        return (_0x18d8x11 ^ _0x18d8x12 ^ _0x18d8x13)
    };
    var _0x18d8x16 = function(_0x18d8x11, _0x18d8x12, _0x18d8x13) {
        return (_0x18d8x12 ^ (_0x18d8x11 | (~_0x18d8x13)))
    };
    var _0x18d8x17 = function(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11, s, _0x18d8x1d) {
        _0x18d8x18 = _0x18d8x8(_0x18d8x18, _0x18d8x8(_0x18d8x8(_0x18d8x10(_0x18d8x19, _0x18d8x1a, _0x18d8x1b), _0x18d8x11), _0x18d8x1d));
        return _0x18d8x8(_0x18d8x5(_0x18d8x18, s), _0x18d8x19)
    };
    var _0x18d8x1e = function(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11, s, _0x18d8x1d) {
        _0x18d8x18 = _0x18d8x8(_0x18d8x18, _0x18d8x8(_0x18d8x8(_0x18d8x14(_0x18d8x19, _0x18d8x1a, _0x18d8x1b), _0x18d8x11), _0x18d8x1d));
        return _0x18d8x8(_0x18d8x5(_0x18d8x18, s), _0x18d8x19)
    };
    var _0x18d8x1f = function(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11, s, _0x18d8x1d) {
        _0x18d8x18 = _0x18d8x8(_0x18d8x18, _0x18d8x8(_0x18d8x8(_0x18d8x15(_0x18d8x19, _0x18d8x1a, _0x18d8x1b), _0x18d8x11), _0x18d8x1d));
        return _0x18d8x8(_0x18d8x5(_0x18d8x18, s), _0x18d8x19)
    };
    var _0x18d8x20 = function(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11, s, _0x18d8x1d) {
        _0x18d8x18 = _0x18d8x8(_0x18d8x18, _0x18d8x8(_0x18d8x8(_0x18d8x16(_0x18d8x19, _0x18d8x1a, _0x18d8x1b), _0x18d8x11), _0x18d8x1d));
        return _0x18d8x8(_0x18d8x5(_0x18d8x18, s), _0x18d8x19)
    };
    var _0x18d8x21 = function(_0x18d8x3) {
        var _0x18d8x22;
        var _0x18d8x23 = _0x18d8x3['length'];
        var _0x18d8x24 = _0x18d8x23 + 8;
        var _0x18d8x25 = (_0x18d8x24 - (_0x18d8x24 % 64)) / 64;
        var _0x18d8x26 = (_0x18d8x25 + 1) * 16;
        var _0x18d8x27 = new Array(_0x18d8x26 - 1);
        var _0x18d8x28 = 0;
        var _0x18d8x29 = 0;
        while (_0x18d8x29 < _0x18d8x23) {
            _0x18d8x22 = (_0x18d8x29 - (_0x18d8x29 % 4)) / 4;
            _0x18d8x28 = (_0x18d8x29 % 4) * 8;
            _0x18d8x27[_0x18d8x22] = (_0x18d8x27[_0x18d8x22] | (_0x18d8x3['charCodeAt'](_0x18d8x29) << _0x18d8x28));
            _0x18d8x29++
        };
        _0x18d8x22 = (_0x18d8x29 - (_0x18d8x29 % 4)) / 4;
        _0x18d8x28 = (_0x18d8x29 % 4) * 8;
        _0x18d8x27[_0x18d8x22] = _0x18d8x27[_0x18d8x22] | (0x80 << _0x18d8x28);
        _0x18d8x27[_0x18d8x26 - 2] = _0x18d8x23 << 3;
        _0x18d8x27[_0x18d8x26 - 1] = _0x18d8x23 >>> 29;
        return _0x18d8x27
    };
    var _0x18d8x2a = function(_0x18d8x6) {
        var _0x18d8x2b = '',
            _0x18d8x2c = '',
            _0x18d8x2d, _0x18d8x2e;
        for (_0x18d8x2e = 0; _0x18d8x2e <= 3; _0x18d8x2e++) {
            _0x18d8x2d = (_0x18d8x6 >>> (_0x18d8x2e * 8)) & 255;
            _0x18d8x2c = '0' + _0x18d8x2d.toString(16);
            _0x18d8x2b = _0x18d8x2b + _0x18d8x2c['substr'](_0x18d8x2c['length'] - 2, 2)
        };
        return _0x18d8x2b
    };

    function _0x18d8x2f(_0x18d8x30) {
        if (_0x18d8x30 === null || typeof _0x18d8x30 === 'undefined') {
            return ''
        };
        var _0x18d8x31 = (_0x18d8x30 + '');
        var _0x18d8x32 = '',
            _0x18d8x33, _0x18d8x34;
        _0x18d8x33 = _0x18d8x34 = 0;
        var _0x18d8x35 = _0x18d8x31['length'];
        for (var _0x18d8x36 = 0; _0x18d8x36 < _0x18d8x35; _0x18d8x36++) {
            var _0x18d8x37 = _0x18d8x31['charCodeAt'](_0x18d8x36);
            var _0x18d8x38 = null;
            if (_0x18d8x37 < 128) {
                _0x18d8x34++
            } else {
                if (_0x18d8x37 > 127 && _0x18d8x37 < 2048) {
                    _0x18d8x38 = String['fromCharCode']((_0x18d8x37 >> 6) | 192, (_0x18d8x37 & 63) | 128)
                } else {
                    if ((_0x18d8x37 & 0xF800) != 0xD800) {
                        _0x18d8x38 = String['fromCharCode']((_0x18d8x37 >> 12) | 224, ((_0x18d8x37 >> 6) & 63) | 128, (_0x18d8x37 & 63) | 128)
                    } else {
                        if ((_0x18d8x37 & 0xFC00) != 0xD800) {
                            throw new RangeError('Unmatched trail surrogate at ' + _0x18d8x36)
                        };
                        var _0x18d8x39 = _0x18d8x31['charCodeAt'](++_0x18d8x36);
                        if ((_0x18d8x39 & 0xFC00) != 0xDC00) {
                            throw new RangeError('Unmatched lead surrogate at ' + (_0x18d8x36 - 1))
                        };
                        _0x18d8x37 = ((_0x18d8x37 & 0x3FF) << 10) + (_0x18d8x39 & 0x3FF) + 0x10000;
                        _0x18d8x38 = String['fromCharCode']((_0x18d8x37 >> 18) | 240, ((_0x18d8x37 >> 12) & 63) | 128, ((_0x18d8x37 >> 6) & 63) | 128, (_0x18d8x37 & 63) | 128)
                    }
                }
            };
            if (_0x18d8x38 !== null) {
                if (_0x18d8x34 > _0x18d8x33) {
                    _0x18d8x32 += _0x18d8x31['slice'](_0x18d8x33, _0x18d8x34)
                };
                _0x18d8x32 += _0x18d8x38;
                _0x18d8x33 = _0x18d8x34 = _0x18d8x36 + 1
            }
        };
        if (_0x18d8x34 > _0x18d8x33) {
            _0x18d8x32 += _0x18d8x31['slice'](_0x18d8x33, _0x18d8x35)
        };
        return _0x18d8x32
    }
    var _0x18d8x3a, _0x18d8x3b, _0x18d8x3c, _0x18d8x3d, _0x18d8x3e, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x3f = 7,
        _0x18d8x40 = 12,
        _0x18d8x41 = 17,
        _0x18d8x42 = 22,
        _0x18d8x43 = 5,
        _0x18d8x44 = 9,
        _0x18d8x45 = 14,
        _0x18d8x46 = 20,
        _0x18d8x47 = 4,
        _0x18d8x48 = 11,
        _0x18d8x49 = 16,
        _0x18d8x4a = 23,
        _0x18d8x4b = 6,
        _0x18d8x4c = 10,
        _0x18d8x4d = 15,
        _0x18d8x4e = 21;
    _0x18d8x3 = _0x18d8x2f(_0x18d8x3);
    var _0x18d8x11 = _0x18d8x21(_0x18d8x3);
    _0x18d8x18 = 0x67452301;
    _0x18d8x19 = 0xEFCDAB89;
    _0x18d8x1a = 0x98BADCFE;
    _0x18d8x1b = 0x10325476;
    _0x18d8x4 = _0x18d8x11['length'];
    for (_0x18d8x3a = 0; _0x18d8x3a < _0x18d8x4; _0x18d8x3a += 16) {
        _0x18d8x3b = _0x18d8x18;
        _0x18d8x3c = _0x18d8x19;
        _0x18d8x3d = _0x18d8x1a;
        _0x18d8x3e = _0x18d8x1b;
        _0x18d8x18 = _0x18d8x17(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a], _0x18d8x3f, 0xD76AA478);
        _0x18d8x1b = _0x18d8x17(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 1], _0x18d8x40, 0xE8C7B756);
        _0x18d8x1a = _0x18d8x17(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 2], _0x18d8x41, 0x242070DB);
        _0x18d8x19 = _0x18d8x17(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 3], _0x18d8x42, 0xC1BDCEEE);
        _0x18d8x18 = _0x18d8x17(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 4], _0x18d8x3f, 0xF57C0FAF);
        _0x18d8x1b = _0x18d8x17(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 5], _0x18d8x40, 0x4787C62A);
        _0x18d8x1a = _0x18d8x17(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 6], _0x18d8x41, 0xA8304613);
        _0x18d8x19 = _0x18d8x17(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 7], _0x18d8x42, 0xFD469501);
        _0x18d8x18 = _0x18d8x17(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 8], _0x18d8x3f, 0x698098D8);
        _0x18d8x1b = _0x18d8x17(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 9], _0x18d8x40, 0x8B44F7AF);
        _0x18d8x1a = _0x18d8x17(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 10], _0x18d8x41, 0xFFFF5BB1);
        _0x18d8x19 = _0x18d8x17(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 11], _0x18d8x42, 0x895CD7BE);
        _0x18d8x18 = _0x18d8x17(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 12], _0x18d8x3f, 0x6B901122);
        _0x18d8x1b = _0x18d8x17(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 13], _0x18d8x40, 0xFD987193);
        _0x18d8x1a = _0x18d8x17(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 14], _0x18d8x41, 0xA679438E);
        _0x18d8x19 = _0x18d8x17(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 15], _0x18d8x42, 0x49B40821);
        _0x18d8x18 = _0x18d8x1e(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 1], _0x18d8x43, 0xF61E2562);
        _0x18d8x1b = _0x18d8x1e(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 6], _0x18d8x44, 0xC040B340);
        _0x18d8x1a = _0x18d8x1e(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 11], _0x18d8x45, 0x265E5A51);
        _0x18d8x19 = _0x18d8x1e(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a], _0x18d8x46, 0xE9B6C7AA);
        _0x18d8x18 = _0x18d8x1e(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 5], _0x18d8x43, 0xD62F105D);
        _0x18d8x1b = _0x18d8x1e(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 10], _0x18d8x44, 0x2441453);
        _0x18d8x1a = _0x18d8x1e(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 15], _0x18d8x45, 0xD8A1E681);
        _0x18d8x19 = _0x18d8x1e(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 4], _0x18d8x46, 0xE7D3FBC8);
        _0x18d8x18 = _0x18d8x1e(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 9], _0x18d8x43, 0x21E1CDE6);
        _0x18d8x1b = _0x18d8x1e(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 14], _0x18d8x44, 0xC33707D6);
        _0x18d8x1a = _0x18d8x1e(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 3], _0x18d8x45, 0xF4D50D87);
        _0x18d8x19 = _0x18d8x1e(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 8], _0x18d8x46, 0x455A14ED);
        _0x18d8x18 = _0x18d8x1e(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 13], _0x18d8x43, 0xA9E3E905);
        _0x18d8x1b = _0x18d8x1e(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 2], _0x18d8x44, 0xFCEFA3F8);
        _0x18d8x1a = _0x18d8x1e(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 7], _0x18d8x45, 0x676F02D9);
        _0x18d8x19 = _0x18d8x1e(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 12], _0x18d8x46, 0x8D2A4C8A);
        _0x18d8x18 = _0x18d8x1f(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 5], _0x18d8x47, 0xFFFA3942);
        _0x18d8x1b = _0x18d8x1f(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 8], _0x18d8x48, 0x8771F681);
        _0x18d8x1a = _0x18d8x1f(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 11], _0x18d8x49, 0x6D9D6122);
        _0x18d8x19 = _0x18d8x1f(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 14], _0x18d8x4a, 0xFDE5380C);
        _0x18d8x18 = _0x18d8x1f(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 1], _0x18d8x47, 0xA4BEEA44);
        _0x18d8x1b = _0x18d8x1f(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 4], _0x18d8x48, 0x4BDECFA9);
        _0x18d8x1a = _0x18d8x1f(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 7], _0x18d8x49, 0xF6BB4B60);
        _0x18d8x19 = _0x18d8x1f(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 10], _0x18d8x4a, 0xBEBFBC70);
        _0x18d8x18 = _0x18d8x1f(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 13], _0x18d8x47, 0x289B7EC6);
        _0x18d8x1b = _0x18d8x1f(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a], _0x18d8x48, 0xEAA127FA);
        _0x18d8x1a = _0x18d8x1f(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 3], _0x18d8x49, 0xD4EF3085);
        _0x18d8x19 = _0x18d8x1f(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 6], _0x18d8x4a, 0x4881D05);
        _0x18d8x18 = _0x18d8x1f(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 9], _0x18d8x47, 0xD9D4D039);
        _0x18d8x1b = _0x18d8x1f(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 12], _0x18d8x48, 0xE6DB99E5);
        _0x18d8x1a = _0x18d8x1f(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 15], _0x18d8x49, 0x1FA27CF8);
        _0x18d8x19 = _0x18d8x1f(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 2], _0x18d8x4a, 0xC4AC5665);
        _0x18d8x18 = _0x18d8x20(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a], _0x18d8x4b, 0xF4292244);
        _0x18d8x1b = _0x18d8x20(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 7], _0x18d8x4c, 0x432AFF97);
        _0x18d8x1a = _0x18d8x20(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 14], _0x18d8x4d, 0xAB9423A7);
        _0x18d8x19 = _0x18d8x20(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 5], _0x18d8x4e, 0xFC93A039);
        _0x18d8x18 = _0x18d8x20(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 12], _0x18d8x4b, 0x655B59C3);
        _0x18d8x1b = _0x18d8x20(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 3], _0x18d8x4c, 0x8F0CCC92);
        _0x18d8x1a = _0x18d8x20(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 10], _0x18d8x4d, 0xFFEFF47D);
        _0x18d8x19 = _0x18d8x20(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 1], _0x18d8x4e, 0x85845DD1);
        _0x18d8x18 = _0x18d8x20(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 8], _0x18d8x4b, 0x6FA87E4F);
        _0x18d8x1b = _0x18d8x20(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 15], _0x18d8x4c, 0xFE2CE6E0);
        _0x18d8x1a = _0x18d8x20(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 6], _0x18d8x4d, 0xA3014314);
        _0x18d8x19 = _0x18d8x20(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 13], _0x18d8x4e, 0x4E0811A1);
        _0x18d8x18 = _0x18d8x20(_0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x11[_0x18d8x3a + 4], _0x18d8x4b, 0xF7537E82);
        _0x18d8x1b = _0x18d8x20(_0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x1a, _0x18d8x11[_0x18d8x3a + 11], _0x18d8x4c, 0xBD3AF235);
        _0x18d8x1a = _0x18d8x20(_0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x19, _0x18d8x11[_0x18d8x3a + 2], _0x18d8x4d, 0x2AD7D2BB);
        _0x18d8x19 = _0x18d8x20(_0x18d8x19, _0x18d8x1a, _0x18d8x1b, _0x18d8x18, _0x18d8x11[_0x18d8x3a + 9], _0x18d8x4e, 0xEB86D391);
        _0x18d8x18 = _0x18d8x8(_0x18d8x18, _0x18d8x3b);
        _0x18d8x19 = _0x18d8x8(_0x18d8x19, _0x18d8x3c);
        _0x18d8x1a = _0x18d8x8(_0x18d8x1a, _0x18d8x3d);
        _0x18d8x1b = _0x18d8x8(_0x18d8x1b, _0x18d8x3e)
    };
    var _0x18d8x4f = _0x18d8x2a(_0x18d8x18) + _0x18d8x2a(_0x18d8x19) + _0x18d8x2a(_0x18d8x1a) + _0x18d8x2a(_0x18d8x1b);
    return _0x18d8x4f['toLowerCase']()
}
var wbJq;
var WebRTClient;
var xhrPool = [];
var WebRTCClientInterface = function() {
    var _0x18d8x54 = null;
    var _0x18d8x55 = this;
    var _0x18d8x56 = false;
    var _0x18d8x57 = false;
    var _0x18d8x58, _0x18d8x59 = {};
    if (window['location']['href']['indexOf']('app_dev.php') + 1) {
        _0x18d8x59 = window['location']['origin'];
        _0x18d8x58 = {
            ru: window['location']['origin'],
            en: window['location']['origin'],
            cs: window['location']['origin'],
            uk: window['location']['origin']
        }
    } else {
        _0x18d8x59 = 'https://www.sipnet.ru';
        _0x18d8x58 = {
            ru: 'https://www.sipnet.ru',
            en: 'https://www.sipnet.net',
            cs: 'https://www.sipnet.net',
            uk: 'https://www.sipnet.net'
        }
    };
    var _0x18d8x5a = false;
    var _0x18d8x5b = ['https://www.sipnet.ru/webrtc/detector.js'];
    var _0x18d8x5c = ['https://cdnjs.cloudflare.com/ajax/libs/webui-popover/1.2.5/jquery.webui-popover.css'];
    var _0x18d8x5d = ['https://cdnjs.cloudflare.com/ajax/libs/webui-popover/1.2.5/jquery.webui-popover.js'];
    var _0x18d8x5e = [_0x18d8x59 + '/bundles/artsoftemain/js/frontend/lib/webrtc/adapter-ob.min.js', _0x18d8x59 + '/bundles/artsoftemain/js/frontend/lib/webrtc/DTMFgenerator.min.js', _0x18d8x59 + '/bundles/artsoftemain/js/frontend/lib/webrtc/ximssclient.js', 'https://www.sipnet.ru/ximsswrapper.js', _0x18d8x59 + '/bundles/artsoftemain/js/frontend/lib/webrtc/callutils.js'];
    var _0x18d8x5f = false;
    var _0x18d8x60 = null;
    var _0x18d8x61 = '';
    var _0x18d8x62 = '';
    var _0x18d8x63 = '';
    var _0x18d8x64 = '.js-dtmf_button';
    var _0x18d8x65 = '.js-call_client_status_progress';
    var _0x18d8x66 = '.js-webrtc_client_call_timer';
    var _0x18d8x67 = {
        cache: false,
        arrow: true,
        closeable: true,
        trigger: 'manual'
    };
    var _0x18d8x68 = {};
    var _0x18d8x69 = false;
    this['getBasePath'] = function() {
        var _0x18d8x68 = _0x18d8x54(_0x18d8x62)['data']('lang');
        if (_0x18d8x68) {
            _0x18d8x59 = _0x18d8x58[_0x18d8x68['toLowerCase']()]
        } else {
            _0x18d8x59 = _0x18d8x58['ru']
        };
        return _0x18d8x59
    };
    this['callid'] = this['callid'] || Date['now']();
    this['getQueryData'] = function() {
        var _0x18d8x6a = location['search']['substring'](1)['split']('&');
        var _0x18d8x6b = {};
        if (_0x18d8x6a) {
            _0x18d8x6a['forEach'](function(_0x18d8x6c) {
                var _0x18d8x6d = _0x18d8x6c['split']('=');
                _0x18d8x6b[_0x18d8x6d[0]] = _0x18d8x6d[1]
            })
        };
        return _0x18d8x6b
    };
    this['createOptions'] = function(_0x18d8x6e) {
        var _0x18d8x6f = _0x18d8x55['audioInputDevices'];
        if (!_0x18d8x6e) {
            return
        };
        var _0x18d8x70 = _0x18d8x6e['find']('select'),
            _0x18d8x71 = _0x18d8x6e['data']();
        if (!_0x18d8x6f['length']) {
            _0x18d8x6f[0] = _0x18d8x71['devicesNotFound']
        };
        for (var _0x18d8x72 = 0; _0x18d8x72 !== _0x18d8x6f['length']; ++_0x18d8x72) {
            var _0x18d8x73 = _0x18d8x6f[_0x18d8x72];
            var _0x18d8x74 = _0x18d8x54('<option>');
            _0x18d8x74['val'](_0x18d8x73['deviceId']);
            if (_0x18d8x74['val']() === localStorage['getItem']('audio_device')) {
                _0x18d8x74['attr']('selected', 'selected')
            };
            if (_0x18d8x73['kind'] === 'audioinput') {
                console['log']('Audio source: ', _0x18d8x73);
                _0x18d8x74['text'](_0x18d8x73['label'] || 'microphone ' + (_0x18d8x70['length'] + 1));
                _0x18d8x70['append'](_0x18d8x74)
            } else {
                console['log']('Some other kind of source: ', _0x18d8x6f)
            }
        };
        _0x18d8x70['on']('change', function(_0x18d8x75) {
            localStorage['setItem']('audio_device', _0x18d8x54(this)['val']())
        })
    };
    this['getLangs'] = function() {
        var _0x18d8x76 = this;
        _0x18d8x54['ajax']({
            url: _0x18d8x59 + '/bundles/artsoftemain/js/frontend/webrtc-lang.json',
            dataType: 'json',
            async: false,
            success: function(_0x18d8x6b) {
                _0x18d8x76['lang'] = _0x18d8x68 = _0x18d8x6b
            },
            error: function(_0x18d8x77, _0x18d8x78) {}
        })
    };
    this['doTranslation'] = function() {
        var _0x18d8x68 = 'ru';
        var _0x18d8x6a = WebRTClient['getQueryData']();
        if (_0x18d8x6a && _0x18d8x6a['lang']) {
            _0x18d8x68 = _0x18d8x6a['lang']
        };
        if (this['lang']) {
            try {
                _0x18d8x54('.js-text_end_call')['html'](this['lang'][_0x18d8x68]['Complete'])
            } catch (e) {
                return true
            }
        }
    };
    this['bindButton'] = function(btnId) {
        if (_0x18d8x54 == null && typeof wbJq == 'undefined') {
            _0x18d8x54 = jQuery
        } else {
            if (typeof wbJq != 'undefined') {
                _0x18d8x54 = wbJq
            }
        };
        var _0x18d8x76 = this;
        _0x18d8x62 = btnId || '.js-start_client_call';
        _0x18d8x63 = '.js-end_client_call';
        if (_0x18d8x54(_0x18d8x62)['length'] == 0) {
            _0x18d8x62 = '.js-start_call';
            _0x18d8x63 = '.js-end_call'
        };
        if (_0x18d8x54(_0x18d8x62)['length'] > 1) {
            _0x18d8x54(_0x18d8x62)['each'](function() {
                if (!_0x18d8x54(this)['data']('lang')) {
                    _0x18d8x54(this)['data']('lang', 'ru');
                    _0x18d8x54(this)['attr']('data-lang', 'ru')
                }
            })
        } else {
            if (_0x18d8x54(_0x18d8x62)['length'] == 1) {
                if (!_0x18d8x54(_0x18d8x62)['data']('lang')) {
                    _0x18d8x54(_0x18d8x62)['data']('lang', 'ru');
                    _0x18d8x54(_0x18d8x62)['attr']('data-lang', 'ru')
                }
            }
        };
        _0x18d8x54(_0x18d8x62)['click'](function(_0x18d8x75) {
            var _0x18d8x76 = _0x18d8x54(this);
            var _0x18d8x82 = _0x18d8x76['parent']();
            if (!_0x18d8x82['find']('audio, video')['length']) {
                _0x18d8x82['append'](_0x18d8x54('<audio id="audioElem_' + WebRTClient['callid'] + '" autoplay>'))
            };
            var _0x18d8x6a = WebRTClient['getQueryData']();
            var _0x18d8x80 = (_0x18d8x54(this)['data']('token') != null) ? _0x18d8x54(this)['data']('token') : _0x18d8x6a['token'];
            if (document['location']['protocol'] == 'http:' || _0x18d8x54(this)['data']('forcePopup') == true) {
                var _0x18d8x83 = (_0x18d8x5a) ? _0x18d8x59 + '/app_dev.php' : _0x18d8x59;
                var _0x18d8x84 = (_0x18d8x54(this)['data']('dtmf') == 'on');
                var _0x18d8x85 = _0x18d8x54(this)['data']('lang')['toLowerCase']();
                var _0x18d8x86 = _0x18d8x83 + '/webrtc/button?token=' + _0x18d8x80;
                var _0x18d8x87 = 300;
                if (_0x18d8x84) {
                    _0x18d8x86 += '&dtmf=on';
                    _0x18d8x87 = 500
                };
                if (_0x18d8x85) {
                    _0x18d8x86 += '&lang=' + _0x18d8x85
                };
                window['open'](_0x18d8x86, '\u0417\u0432\u043E\u043D\u043E\u043A \u0441 \u0441\u0430\u0439\u0442\u0430', 'width=300,height=' + _0x18d8x87 + ',toolbar=0,status=0,resizable=0,location=0,directories=0,top=100,left=100,location=0,scrollbars=0')
            } else {
                _0x18d8x75['preventDefault']();
                if (_0x18d8x54(this)['hasClass']('disabled')) {
                    return false
                };
                WRCall['setConnectedStatusByToken'](_0x18d8x80);
                if (WRCall['active'] && !WRCall['startCallFreeze']) {
                    WRCall['setStateStartCallButtons'](WRCall.DISABLED);
                    _0x18d8x75['preventDefault']();
                    WRCall['stop']();
                    WRCall['logout']();
                    if (_0x18d8x6a['token']) {
                        setTimeout('window.close()', 1000)
                    };
                    WRCall['startCallFreeze'] = setTimeout(function() {
                        _0x18d8x7f(_0x18d8x80);
                        WRCall['startCallFreeze'] = false
                    }, WRCall['wrapperSessionCloseDelay']);
                    return
                };
                _0x18d8x7f(_0x18d8x80)
            }
        });
    }
    this['init'] = function() {
        if (_0x18d8x54 == null && typeof wbJq == 'undefined') {
            _0x18d8x54 = jQuery
        } else {
            if (typeof wbJq != 'undefined') {
                _0x18d8x54 = wbJq
            }
        };
        _0x18d8x56 = /MSIE/ ['test'](navigator['userAgent']);
        _0x18d8x57 = /MSIE \d{2}.\d/ ['test'](navigator['userAgent']);
        var _0x18d8x76 = this;
        _0x18d8x62 = '.js-start_client_call';
        _0x18d8x63 = '.js-end_client_call';
        if (_0x18d8x54(_0x18d8x62)['length'] == 0) {
            _0x18d8x62 = '.js-start_call';
            _0x18d8x63 = '.js-end_call'
        };
        if (_0x18d8x54(_0x18d8x62)['length'] > 1) {
            _0x18d8x54(_0x18d8x62)['each'](function() {
                if (!_0x18d8x54(this)['data']('lang')) {
                    _0x18d8x54(this)['data']('lang', 'ru');
                    _0x18d8x54(this)['attr']('data-lang', 'ru')
                }
            })
        } else {
            if (_0x18d8x54(_0x18d8x62)['length'] == 1) {
                if (!_0x18d8x54(_0x18d8x62)['data']('lang')) {
                    _0x18d8x54(_0x18d8x62)['data']('lang', 'ru');
                    _0x18d8x54(_0x18d8x62)['attr']('data-lang', 'ru')
                }
            }
        };
        _0x18d8x59 = this['getBasePath']();
        this['getLangs']();
        this['doTranslation']();
        LazyLoad['js'](_0x18d8x5b, function() {
            LazyLoad['css'](_0x18d8x5c, function() {
                if (_0x18d8x56) {
                    _0x18d8x54['getScript'](_0x18d8x5d[0], function() {
                        setTimeout(function() {
                            _0x18d8x76['initForIe']()
                        }, 100)
                    })
                } else {
                    LazyLoad['js'](_0x18d8x5d, function() {
                        _0x18d8x79();
                        _0x18d8x76['initWebrtc']()
                    })
                }
            })
        })
    };
    this['initForIe'] = function() {
        _0x18d8x79();
        this['initWebrtc']()
    };
    var _0x18d8x79 = function() {
        var _0x18d8x7a = false;
        _0x18d8x54['each'](['webkitRTCPeerConnection', 'mozRTCPeerConnection', 'RTCIceGatherer', 'RTCPeerConnection', 'RTCDataChannel'], function(_0x18d8x7b, _0x18d8x6c) {
            if (_0x18d8x6c in window) {
                _0x18d8x7a = true
            }
        });
        var _0x18d8x7c = false;
        if (navigator['getUserMedia'] || navigator['webkitGetUserMedia'] || navigator['mozGetUserMedia']) {
            _0x18d8x7c = true
        } else {
            if (navigator['mediaDevices'] && navigator['mediaDevices']['getUserMedia']) {
                _0x18d8x7c = true
            }
        };
        var _0x18d8x7d = true;
        _0x18d8x54['ajax']({
            url: _0x18d8x59 + '/customer-webrtc-call',
            async: false,
            type: 'POST',
            data: {
                user_agent: navigator['userAgent'],
                detector: detector
            },
            success: function(_0x18d8x7e) {
                _0x18d8x7d = _0x18d8x7e['message']
            }
        });
        if (_0x18d8x7d) {
            _0x18d8x5f = (_0x18d8x7a && _0x18d8x7c) || navigator['userAgent']['toLowerCase']()['indexOf']('bowser') != -1
        };
        return _0x18d8x5f
    };
    this['initWebrtc'] = function() {
        var _0x18d8x76 = this;
        if (_0x18d8x5f) {
            LazyLoad['js'](_0x18d8x5e, function() {
                _0x18d8x76['startWebRTCSupport']()
            })
        } else {
            _0x18d8x9d()
        };
        if (/MSIE 8.0/i ['test'](navigator['userAgent'])) {
            _0x18d8x54('.fw-container__step__form__design-btn__body')['addClass']('no-svg')
        }
    };
    this['startWebRTCSupport'] = function() {
        _0x18d8x81();
        _0x18d8x88();
        var _0x18d8x6a = this['getQueryData']();
        if (_0x18d8x6a['token'] != undefined) {
            _0x18d8x54(_0x18d8x62)['click']();
            _0x18d8x54(window)['bind']('beforeunload', function() {
                ximssSession['doLogout']()
            })
        }
    };
    var _0x18d8x7f = function(_0x18d8x80) {
        _0x18d8x54(document)['trigger']('webrtc.call.pre-init');
        WRCall['token'] = _0x18d8x80;
        if (_0x18d8x54(_0x18d8x62)['find']('.js-phone_call')['length'] > 0) {
            _0x18d8x61 = _0x18d8x54(_0x18d8x62)['find']('.js-phone_call')['text']()
        } else {
            _0x18d8x61 = (typeof _0x18d8x80 != 'undefined') ? _0x18d8x80 : WebRTClient['getQueryData']()['token']
        };
        WebRTClient['hideError']();
        _0x18d8x9a['current'] = 0;
        WRCall['phone'] = _0x18d8x61;
        _0x18d8x54(_0x18d8x62)['attr']('disabled', 'disabled')['addClass']('disabled');
        WRCall['init']();
        WRCall['start']();
        if (_0x18d8x5f) {
            WebRTClient['sendStatistics'](_0x18d8x5f ? 'webrtc' : 'callback', _0x18d8x80)
        }
    };
    var _0x18d8x81 = function() {
        _0x18d8x60 = _0x18d8x54('#ringtone')['get'](0);
        _0x18d8x54(_0x18d8x60)['on']('pause', function() {
            _0x18d8x60['currentTime'] = 0
        });
        _0x18d8x54(window)['on']('unload', function() {
            if (WRCall['active']) {
                WRCall['stop']();
                WRCall['logout']()
            }
        });
        _0x18d8x54(_0x18d8x62)['on']('dbclick', function() {
            return false
        });
        _0x18d8x54('body')['on']('click', _0x18d8x62, function(_0x18d8x75) {
            var _0x18d8x76 = _0x18d8x54(this);
            var _0x18d8x82 = _0x18d8x76['parent']();
            if (!_0x18d8x82['find']('audio, video')['length']) {
                _0x18d8x82['append'](_0x18d8x54('<audio id="audioElem_' + WebRTClient['callid'] + '" autoplay>'))
            };
            var _0x18d8x6a = WebRTClient['getQueryData']();
            var _0x18d8x80 = (_0x18d8x54(this)['data']('token') != null) ? _0x18d8x54(this)['data']('token') : _0x18d8x6a['token'];
            if (document['location']['protocol'] == 'http:' || _0x18d8x54(this)['data']('forcePopup') == true) {
                var _0x18d8x83 = (_0x18d8x5a) ? _0x18d8x59 + '/app_dev.php' : _0x18d8x59;
                var _0x18d8x84 = (_0x18d8x54(this)['data']('dtmf') == 'on');
                var _0x18d8x85 = _0x18d8x54(this)['data']('lang')['toLowerCase']();
                var _0x18d8x86 = _0x18d8x83 + '/webrtc/button?token=' + _0x18d8x80;
                var _0x18d8x87 = 300;
                if (_0x18d8x84) {
                    _0x18d8x86 += '&dtmf=on';
                    _0x18d8x87 = 500
                };
                if (_0x18d8x85) {
                    _0x18d8x86 += '&lang=' + _0x18d8x85
                };
                window['open'](_0x18d8x86, '\u0417\u0432\u043E\u043D\u043E\u043A \u0441 \u0441\u0430\u0439\u0442\u0430', 'width=300,height=' + _0x18d8x87 + ',toolbar=0,status=0,resizable=0,location=0,directories=0,top=100,left=100,location=0,scrollbars=0')
            } else {
                _0x18d8x75['preventDefault']();
                if (_0x18d8x54(this)['hasClass']('disabled')) {
                    return false
                };
                WRCall['setConnectedStatusByToken'](_0x18d8x80);
                if (WRCall['active'] && !WRCall['startCallFreeze']) {
                    WRCall['setStateStartCallButtons'](WRCall.DISABLED);
                    _0x18d8x75['preventDefault']();
                    WRCall['stop']();
                    WRCall['logout']();
                    if (_0x18d8x6a['token']) {
                        setTimeout('window.close()', 1000)
                    };
                    WRCall['startCallFreeze'] = setTimeout(function() {
                        _0x18d8x7f(_0x18d8x80);
                        WRCall['startCallFreeze'] = false
                    }, WRCall['wrapperSessionCloseDelay']);
                    return
                };
                _0x18d8x7f(_0x18d8x80)
            }
        });
        WebRTClient['initDTMF'](_0x18d8x54(_0x18d8x64));
        _0x18d8x54('body')['on']('click', _0x18d8x63, function(_0x18d8x75) {
            var _0x18d8x6a = WebRTClient['getQueryData']();
            var _0x18d8x80 = (_0x18d8x54(this)['data']('token') != null) ? _0x18d8x54(this)['data']('token') : _0x18d8x6a['token'];
            if (!_0x18d8x54(this)['hasClass']('disabled')) {
                WRCall['token'] = _0x18d8x80;
                _0x18d8x75['preventDefault']();
                WRCall['stop']();
                WRCall['logout']();
                if (_0x18d8x6a['token']) {
                    setTimeout('window.close()', 1000)
                }
            }
        })
    };
    var _0x18d8x88 = function() {
        ximssSession['onXimssCallProvisioned'] = function() {
            if (WRCall['active']) {
                var _0x18d8x89 = _0x18d8x54('label[data-token=' + WRCall['token'] + ']'),
                    _0x18d8x8a = _0x18d8x54('.js-webrtc_call_status');
                _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
                _0x18d8x54('label[data-token=' + WRCall['token'] + '] .js-text_call')['text'](WRCall['btnEndText']);
                _0x18d8x54(_0x18d8x65)['html'](_0x18d8x8a['data']('calling'));
                _0x18d8x8a['find']('.webrtc-calls-text')['html'](WebRTClient['lang']['Calling']);
                _0x18d8x89['parent']()['addClass']('fw-container__step__form__design-btn__body--dtmf');
                _0x18d8x89['parent']()['find'](_0x18d8x64)['show']();
                _0x18d8x69 = true
            }
        };
        ximssSession['onXimssCallConnected'] = function() {
            var _0x18d8x8a = _0x18d8x54('.js-webrtc_call_status');
            if (WRCall['active'] && !_0x18d8x69) {
                var _0x18d8x89 = _0x18d8x54('label[data-token=' + WRCall['token'] + ']');
                _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
                _0x18d8x54('label[data-token=' + WRCall['token'] + '] .js-text_call')['text'](WRCall['btnEndText']);
                _0x18d8x54(_0x18d8x65)['html'](_0x18d8x8a['data']('calling'));
                _0x18d8x8a['find']('.webrtc-calls-text')['html'](WebRTClient['lang'][WRCall['lang']]['Calling']);
                _0x18d8x89['parent']()['addClass']('fw-container__step__form__design-btn__body--dtmf');
                _0x18d8x89['parent']()['find'](_0x18d8x64)['show']()
            };
            if (WRCall['active']) {
                _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
                _0x18d8x54(_0x18d8x65)['html'](_0x18d8x8a['data']('talking'));
                _0x18d8x8a['find']('.webrtc-calls-text')['html'](WebRTClient['lang'][WRCall['lang']]['Talking']);
                _0x18d8x9a['start']();
                ximssSession['doDTMFCreate'](WebRTClient['callid']);
                _0x18d8x54(_0x18d8x63)['find']('.js-call_phone_btn_sub')['removeClass']('disabled');
                _0x18d8x69 = false
            }
        };
        ximssSession['onXimssCallDisconnected'] = function(_0x18d8x8b, _0x18d8x8c) {
            _0x18d8x54(_0x18d8x63)['parent']()['removeClass']('fw-container__step__form__design-btn__body--dtmf');
            _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x64)['hide']();
            if (_0x18d8x8b || _0x18d8x8c) {
                var _0x18d8x8d = (typeof WebRTClient['lang'] != 'undefined' && typeof WebRTClient['lang'][WRCall['lang']][_0x18d8x8b] != 'undefined') ? WebRTClient['lang'][WRCall['lang']][_0x18d8x8b] : _0x18d8x8b;
                var _0x18d8x6a = WebRTClient['getQueryData']();
                if (_0x18d8x6a['token'] && _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x65)['length'] > 0) {
                    _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x65)['html'](_0x18d8x8d);
                    _0x18d8x54(_0x18d8x63)['parent']()['find']('button')['addClass']('disabled')['attr']('disabled', 'disabled')
                } else {
                    _0x18d8x54(_0x18d8x63)['webuiPopover']('destroy')['webuiPopover'](_0x18d8x54['extend']({}, _0x18d8x67, {
                        content: _0x18d8x8d
                    }))['webuiPopover']('show')
                }
            };
            _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
            WRCall['stop']();
            WRCall['logout']();
            if (_0x18d8x6a['token']) {
                setTimeout('window.close()', 1000)
            }
        };
        ximssSession['onXimssSuccessLogin'] = function() {
            ximssSession['ximssSignalBindForDevice'](false)
        };
        ximssSession['onXimssSignalBind'] = function() {};
        ximssSession['onXimssSignalBindForDevice'] = function() {
            WRCall['startCall']()
        };
        ximssSession['onXimssForceClosed'] = function() {
            console['log']('Force closed broken session');
            WRCall['reloadAfterClosedSession']()
        };
        ximssSession['onXimssErrorLogin'] = function(_0x18d8x8b) {
            if (_0x18d8x8b) {
                var _0x18d8x8d = (typeof WebRTClient['lang'] != 'undefined' && typeof WebRTClient['lang'][WRCall['lang']][_0x18d8x8b] != 'undefined') ? WebRTClient['lang'][WRCall['lang']][_0x18d8x8b] : _0x18d8x8b;
                var _0x18d8x6a = WebRTClient['getQueryData']();
                if (_0x18d8x6a['token'] && _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x65)['length'] > 0) {
                    _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x65)['html'](_0x18d8x8d);
                    _0x18d8x54(_0x18d8x63)['parent']()['find']('button')['addClass']('disabled')['attr']('disabled', 'disabled')
                } else {
                    _0x18d8x54(_0x18d8x63)['webuiPopover']('destroy')['webuiPopover'](_0x18d8x54['extend']({}, _0x18d8x67, {
                        content: _0x18d8x8d
                    }))['webuiPopover']('show')
                }
            };
            _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
            WRCall['stop']()
        };
        ximssSession['onXimssError'] = function(_0x18d8x8e) {
            _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
            WRCall['stop']();
            WRCall['logout']();
            WebRTClient['showError'](_0x18d8x8e)
        };
        ximssSession['onXimssReadIM'] = function(_0x18d8x8f, _0x18d8x90, _0x18d8x91) {};
        ximssSession['onXimssCallAccept'] = function() {
            _0x18d8x60['pause']();
            if (WRCall['active']) {
                _0x18d8x54(_0x18d8x65)['html'](_0x18d8x54('.js-webrtc_call_status')['data']('talking'));
                _0x18d8x9a['current'] = 0;
                _0x18d8x9a['start']();
                _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled')
            }
        };
        ximssSession['onNetworkError'] = function(_0x18d8x8b) {
            if (_0x18d8x8b) {
                var _0x18d8x8d = (typeof WebRTClient['lang'] != 'undefined' && typeof WebRTClient['lang'][WRCall['lang']][_0x18d8x8b] != 'undefined') ? WebRTClient['lang'][WRCall['lang']][_0x18d8x8b] : _0x18d8x8b;
                var _0x18d8x6a = WebRTClient['getQueryData']();
                if (_0x18d8x6a['token'] && _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x65)['length'] > 0) {
                    _0x18d8x54(_0x18d8x63)['parent']()['find'](_0x18d8x65)['html'](_0x18d8x8d);
                    _0x18d8x54(_0x18d8x63)['parent']()['find']('button')['addClass']('disabled')['attr']('disabled', 'disabled')
                } else {
                    _0x18d8x54(_0x18d8x63)['webuiPopover']('destroy')['webuiPopover'](_0x18d8x54['extend']({}, _0x18d8x67, {
                        content: _0x18d8x8d
                    }))['webuiPopover']('show')
                }
            };
            _0x18d8x54(_0x18d8x63)['removeAttr']('disabled')['removeClass']('disabled');
            WRCall['kill']();
            if (_0x18d8x6a['token']) {
                setTimeout('window.close()', 1000)
            }
        }
    };
    var _0x18d8x92 = [];
    WRCall = {
        phone: null,
        active: false,
        incoming: false,
        freezed: false,
        user: 'buttons',
        pass: '4655441888',
        btnDefaultText: null,
        btnEndText: '\u0417\u0430\u0432\u0435\u0440\u0448\u0438\u0442\u044C',
        btnConnectingText: '\u0421\u043E\u0435\u0434\u0438\u043D\u0435\u043D\u0438\u0435...',
        btnDisconnectingText: '\u041E\u0442\u043A\u043B\u044E\u0447\u0435\u043D\u0438\u0435...',
        wrapperSessionCloseDelay: 2100,
        token: null,
        statid: null,
        lang: 'ru',
        ENABLED: true,
        DISABLED: false,
        getDefaultText: function(_0x18d8x93) {
            if (_0x18d8x92['hasOwnProperty'](_0x18d8x93)) {
                return _0x18d8x92[_0x18d8x93]
            };
            this['setStateEndCallButtons'](WRCall.DISABLED);
            this['setStateStartCallButtons'](WRCall.DISABLED)
        },
        setDefaultText: function(_0x18d8x93, _0x18d8x94) {
            if (!_0x18d8x92['hasOwnProperty'](_0x18d8x93)) {
                _0x18d8x92[_0x18d8x93] = _0x18d8x94
            }
        },
        init: function() {
            this['active'] = true;
            _0x18d8x54(document)['trigger']('webrtc.call.init')
        },
        setConnectedStatusByToken: function(_0x18d8x80) {
            _0x18d8x54(_0x18d8x63)['show']()['attr']('disabled', 'disabled')['addClass']('disabled');
            this['token'] = _0x18d8x80;
            this['doTranslate']();
            var _0x18d8x95 = _0x18d8x54(_0x18d8x62 + '[data-token=' + _0x18d8x80 + ']');
            _0x18d8x95['addClass'](_0x18d8x63['replace']('.', ''))['removeClass'](_0x18d8x62['replace']('.', ''));
            var _0x18d8x96 = _0x18d8x54(_0x18d8x63 + '[data-token=' + _0x18d8x80 + ']')['find']('.js-text_call')['get'](0);
            this['setDefaultText'](_0x18d8x80, _0x18d8x54(_0x18d8x96)['text']());
            _0x18d8x54('label[data-token=' + _0x18d8x80 + '] .js-text_call')['html'](this['btnConnectingText'])
        },
        doTranslate: function() {
            var _0x18d8x6a = WebRTClient['getQueryData']();
            var _0x18d8x89 = _0x18d8x54('label[data-token=' + this['token'] + ']');
            if (_0x18d8x89['data']('lang')) {
                this['lang'] = _0x18d8x89['data']('lang')['toLowerCase']()
            } else {
                if (_0x18d8x6a['lang']) {
                    this['lang'] = _0x18d8x6a['lang']['toLowerCase']()
                }
            };
            if (WebRTClient['lang']) {
                this['btnEndText'] = WebRTClient['lang'][this['lang']]['Complete'];
                this['btnConnectingText'] = WebRTClient['lang'][this['lang']]['Connecting']
            }
        },
        login: function() {
            ximssSession['doLogin'](this['user'], this['pass'], 'sipnet.ru')
        },
        start: function() {
            if (ximssSession['theSession']) {
                ximssSession['onXimssSignalBindForDevice'](false);
                return
            };
            if (typeof this['user'] != 'string') {
                this['pass'] = this['pass'].toString()
            };
            if (typeof this['pass'] != 'string') {
                this['pass'] = this['pass'].toString()
            };
            this['login']()
        },
        startCall: function() {
            this['active'] = true;
            var _0x18d8x8a = _0x18d8x54('.js-webrtc_call_status');
            ximssSession['doStartCall'](this['phone'], WebRTClient['callid'], 'default');
            _0x18d8x54(_0x18d8x62)['removeAttr']('disabled')['removeClass']('disabled');
            _0x18d8x54(_0x18d8x65)['html'](_0x18d8x8a['data']('connecting'));
            if (WebRTClient['lang']) {
                _0x18d8x8a['find']('.webrtc-calls-text')['html'](WebRTClient['lang'][WRCall['lang']]['Connecting'])
            };
            _0x18d8x54(document)['trigger']('webrtc.call.start')
        },
        stop: function() {
            var _0x18d8x80 = this['token'];
            if (ximssSession['theSession']) {
                ximssSession['doCallKill'](WebRTClient['callid'])
            };
            if (ximssSession['localStream']) {
                if (navigator['userAgent']['toLowerCase']()['indexOf']('bowser') == -1) {
                    ximssSession['localStream']['getTracks']()[0]['stop']()
                }
            };
            this['active'] = false;
            this['incoming'] = false;
            this['statid'] = null;
            _0x18d8x54('label[data-token=' + _0x18d8x80 + '] .js-text_call')['html'](WebRTClient['lang'][WRCall['lang']]['Disconnecting']);
            this['setStateEndCallButtons'](WRCall.DISABLED);
            this['setStateStartCallButtons'](WRCall.DISABLED);
            _0x18d8x54(_0x18d8x63)['show']()['attr']('disabled', 'disabled')['addClass']('disabled');
            _0x18d8x54(_0x18d8x66)['text']('')
        },
        setStateStartCallButtons: function(_0x18d8x97) {
            var _0x18d8x98 = _0x18d8x54(_0x18d8x62);
            if (_0x18d8x97) {
                _0x18d8x98['removeAttr']('disabled')['removeClass']('disabled')
            } else {
                _0x18d8x98['show']()['attr']('disabled', 'disabled')['addClass']('disabled')
            }
        },
        setStateEndCallButtons: function(_0x18d8x99) {
            var _0x18d8x98 = _0x18d8x54(_0x18d8x63);
            if (_0x18d8x99) {
                _0x18d8x98['removeAttr']('disabled')['removeClass']('disabled')
            } else {
                _0x18d8x98['show']()['attr']('disabled', 'disabled')['addClass']('disabled')
            }
        },
        reloadAfterClosedSession: function() {
            var _0x18d8x80 = this['token'];
            var _0x18d8x8a = _0x18d8x54('.js-webrtc_call_status'),
                _0x18d8x95 = _0x18d8x54('label[data-token=' + _0x18d8x80 + ']');
            _0x18d8x95['addClass'](_0x18d8x62['replace']('.', ''))['removeClass'](_0x18d8x63['replace']('.', ''));
            _0x18d8x95['find']('.js-text_call')['text'](this['getDefaultText'](_0x18d8x80));
            _0x18d8x95['parent']()['removeClass']('fw-container__step__form__design-btn__body--dtmf')['find'](_0x18d8x64)['hide']();
            _0x18d8x54(_0x18d8x66)['text']('');
            _0x18d8x54(_0x18d8x62)['removeAttr']('disabled')['removeClass']('disabled');
            _0x18d8x54(_0x18d8x65)['html'](_0x18d8x8a['data']('cancelling'));
            if (WebRTClient['lang']) {
                _0x18d8x8a['find']('.webrtc-calls-text')['html'](WebRTClient['lang'][this['lang']]['Cancelling'])
            };
            this['setStateStartCallButtons'](WRCall.ENABLED);
            _0x18d8x54(document)['trigger']('webrtc.call.stop');
            _0x18d8x60['pause']()
        },
        logout: function() {
            if (ximssSession['theSession'] && !WRCall['logoutFreeze']) {
                ximssSession['doLogout']();
                WRCall['logoutFreeze'] = setTimeout(function() {
                    WRCall['reloadAfterClosedSession']();
                    WRCall['logoutFreeze'] = false
                }, WRCall['wrapperSessionCloseDelay'])
            }
        },
        answer: function() {
            if (ximssSession['theSession']) {
                ximssSession['initRTCAnswer'](WebRTClient['callid'], 'default')
            };
            this['active'] = true;
            _0x18d8x54(_0x18d8x62)['hide']();
            _0x18d8x54(_0x18d8x63)['show']()
        },
        cancel: function() {
            if (WRCall['incoming'] && ximssSession['theSession']) {
                ximssSession['ximssCallReject'](WebRTClient['callid'])
            };
            this['incoming'] = false;
            this['active'] = false;
            _0x18d8x60['pause']()
        },
        hold: function() {
            ximssSession['doHold'](WebRTClient['callid'])
        },
        unhold: function() {
            ximssSession['doUnhold'](WebRTClient['callid'])
        },
        mute: function() {
            ximssSession['doMute'](WebRTClient['callid'])
        },
        unmute: function() {
            ximssSession['doUnmute'](WebRTClient['callid'])
        },
        rcOn: function() {},
        rcOff: function() {},
        kill: function() {
            var _0x18d8x80 = this['token'];
            _0x18d8x54(_0x18d8x63)['addClass'](_0x18d8x62['replace']('.', ''))['removeClass'](_0x18d8x63['replace']('.', ''));
            _0x18d8x54('label[data-token=' + _0x18d8x80 + '] .js-text_call')['text'](this['getDefaultText'](_0x18d8x80));
            _0x18d8x54(_0x18d8x66)['text']('');
            _0x18d8x54(_0x18d8x62)['removeAttr']('disabled')['removeClass']('disabled')
        }
    };
    var _0x18d8x9a = {
        current: 0,
        interval: 1000,
        intervalId: 0,
        currentToTime: function() {
            var _0x18d8x9b = Math['floor'](this['current'] / 60);
            var _0x18d8x9c = this['current'] - _0x18d8x9b * 60;
            if (_0x18d8x9b < 10) {
                _0x18d8x9b = '0' + _0x18d8x9b
            };
            if (_0x18d8x9c < 10) {
                _0x18d8x9c = '0' + _0x18d8x9c
            };
            return _0x18d8x9b + ':' + _0x18d8x9c
        },
        start: function() {
            if (this['intervalId']) {
                clearInterval(this['intervalId'])
            };
            _0x18d8x54(_0x18d8x66)['text'](this['currentToTime']());
            var _0x18d8x76 = this;
            this['intervalId'] = setInterval(function() {
                _0x18d8x76['tick']()
            }, this['interval'])
        },
        tick: function() {
            this['current']++;
            this['global']++;
            _0x18d8x54(_0x18d8x66)['text'](this['currentToTime']())
        },
        stop: function() {
            if (this['intervalId']) {
                clearInterval(this['intervalId'])
            };
            if (this['current'] > 0) {
                this['current'] = 0
            }
        }
    };
    var _0x18d8x9d = function() {
        LazyLoad['js'](['https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js'], function() {
            _0x18d8x9e()
        })
    };
    var _0x18d8x9e = function() {
        var _0x18d8x83 = (_0x18d8x5a) ? _0x18d8x59 + '/app_dev.php' : _0x18d8x59;
        _0x18d8x54(document)['on']('click', '#CallSource', function() {
            _0x18d8x54('#btn_call_status')['remove']()
        });
        _0x18d8x54(document)['on']('click', '#callPhoneNumberForm button', function() {
            WebRTClient['renderCallForm'](this)
        });
        _0x18d8x54(document)['on']('submit', '#callPhoneNumberForm', function() {
            WebRTClient['renderCallForm'](this);
            return false
        });
        if (_0x18d8x56 && window['XDomainRequest'] && !_0x18d8x57) {
            var _0x18d8x9f = new XDomainRequest();
            _0x18d8x9f['open']('get', _0x18d8x83 + '/webrtc/callButtonForm');
            _0x18d8x9f['onload'] = function() {
                _0x18d8x54(_0x18d8x62)['webuiPopover']('destroy')['webuiPopover'](_0x18d8x54['extend']({}, _0x18d8x67, {
                    type: 'html',
                    content: function() {
                        var _0x18d8xa0 = 0,
                            _0x18d8xa1 = 1000000000000;
                        var _0x18d8xa2 = Math['floor'](Math['random']() * (_0x18d8xa1 - _0x18d8xa0)) + _0x18d8xa0;
                        var _0x18d8xa3 = _0x18d8x54['parseJSON'](_0x18d8x9f['responseText']);
                        var _0x18d8xa4 = _0x18d8x54['parseHTML'](_0x18d8xa3);
                        var _0x18d8x80 = (_0x18d8x54(this)['data']('token') != null) ? _0x18d8x54(this)['data']('token') : WebRTClient['getQueryData']()['token'];
                        var _0x18d8xa5 = md5(_0x18d8xa2 + new Date()['getTime']() + new Date()['getMilliseconds']() + navigator['userAgent']);
                        _0x18d8x54(_0x18d8xa4)['find']('input[name=id]')['val'](_0x18d8x80.toString());
                        _0x18d8x54(_0x18d8xa4)['find']('input[name=page]')['val'](window['location']['href']);
                        _0x18d8x54(_0x18d8xa4)['find']('input[name=statid]')['val'](_0x18d8xa5);
                        return _0x18d8x54(_0x18d8xa4)['clone']()['wrap']('<div>')['parent']()['html']()
                    }
                }))
            };
            _0x18d8x9f['send']()
        } else {
            _0x18d8x54(_0x18d8x62)['webuiPopover']('destroy')['webuiPopover'](_0x18d8x54['extend']({}, _0x18d8x67, {
                type: 'async',
                url: _0x18d8x83 + '/webrtc/callButtonForm',
                content: function(_0x18d8xa3) {
                    var _0x18d8xa0 = 0,
                        _0x18d8xa1 = 1000000000000;
                    var _0x18d8xa2 = Math['floor'](Math['random']() * (_0x18d8xa1 - _0x18d8xa0)) + _0x18d8xa0;
                    var _0x18d8xa4 = _0x18d8x54['parseHTML'](_0x18d8xa3);
                    var _0x18d8x80 = (_0x18d8x54(this)['data']('token') != null) ? _0x18d8x54(this)['data']('token') : WebRTClient['getQueryData']()['token'];
                    var _0x18d8xa5 = md5(_0x18d8xa2 + new Date()['getTime']() + new Date()['getMilliseconds']() + navigator['userAgent']);
                    _0x18d8x54(_0x18d8xa4)['find']('input[name=id]')['val'](_0x18d8x80.toString());
                    _0x18d8x54(_0x18d8xa4)['find']('input[name=page]')['val'](window['location']['href']);
                    _0x18d8x54(_0x18d8xa4)['find']('input[name=statid]')['val'](_0x18d8xa5);
                    return _0x18d8x54(_0x18d8xa4)['clone'](true)['wrap']('<div>')['parent']()['html']()
                },
                placement: function() {
                    if (_0x18d8x54(window)['width']() < 600) {
                        return 'vertical'
                    };
                    return 'auto'
                }
            }))
        };
        _0x18d8x54(_0x18d8x62)['off']()['on']('touchstart click', function() {
            _0x18d8x54(this)['webuiPopover']('show');
            return false
        })
    };
    this['renderCallForm'] = function(_0x18d8xa6) {
        var _0x18d8xa7 = (_0x18d8x54(_0x18d8xa6)['is']('form')) ? _0x18d8x54(_0x18d8xa6) : _0x18d8x54(_0x18d8xa6)['parents']('form#callPhoneNumberForm');
        var _0x18d8x83 = (_0x18d8x5a) ? _0x18d8x59 + '/app_dev.php' : _0x18d8x59;
        _0x18d8xa7['find']('#btn_call_status')['remove']();
        if (_0x18d8xa7['find']('#CallSource') && !/^\d+$/ ['test'](_0x18d8xa7['find']('#CallSource')['val']())) {
            var _0x18d8x8d = _0x18d8xa7['data']('incorrectMessage');
            _0x18d8xa7['find']('input[name=phone]')['after']('<span id="btn_call_status" class="remark remark-hidden">' + _0x18d8x8d + '</span>');
            return false
        };
        if (_0x18d8x56 && window['XDomainRequest']) {
            var _0x18d8x9f = new XDomainRequest();
            _0x18d8x9f['open']('POST', _0x18d8x83 + _0x18d8xa7['attr']('action') + '?' + _0x18d8xa7['serialize']() + '&ie=1');
            _0x18d8x9f['onload'] = function() {
                var _0x18d8x8d;
                if (_0x18d8x54['parseJSON'](_0x18d8x9f['responseText']) == 'Ok') {
                    _0x18d8x8d = _0x18d8xa7['data']('successMessage')
                } else {
                    _0x18d8x8d = _0x18d8x54['parseJSON'](_0x18d8x9f['responseText'])
                };
                _0x18d8xa7['find']('input[name=phone]')['after']('<span id="btn_call_status" class="remark remark-hidden">' + _0x18d8x8d + '</span>')
            };
            _0x18d8x9f['send']()
        } else {
            _0x18d8x54['ajax']({
                url: _0x18d8x83 + _0x18d8xa7['attr']('action'),
                type: 'post',
                data: _0x18d8xa7['serialize'](),
                success: function(_0x18d8x7e) {
                    var _0x18d8x8d;
                    if (_0x18d8x7e == 'Ok') {
                        _0x18d8x8d = _0x18d8xa7['data']('successMessage');
                        _0x18d8xa7['find']('button')['addClass']('disabled');
                        setTimeout(function() {
                            _0x18d8xa7['find']('button')['removeClass']('disabled')
                        }, 10000)
                    } else {
                        _0x18d8x8d = _0x18d8x7e
                    };
                    _0x18d8xa7['find']('input[name=phone]')['after']('<span id="btn_call_status" class="remark remark-hidden">' + _0x18d8x8d + '</span>')
                }
            })
        }
    };
    this['showError'] = function(_0x18d8xa8) {
        var _0x18d8xa7 = _0x18d8x54('.call-status');
        _0x18d8xa7['html'](_0x18d8xa8)['find']('.webrtc-calls-text')['html'](_0x18d8xa8);
        _0x18d8x54(_0x18d8x62)['parent']()['find']('button')['addClass']('disabled')['attr']('disabled', 'disabled')
    };
    this['hideError'] = function() {
        _0x18d8x54('div.alert-danger[role=alert]')['remove']()
    };
    this['sendStatistics'] = function(_0x18d8xa9, _0x18d8xaa, _0x18d8xab) {
        if (_0x18d8xab == null) {
            _0x18d8xab = window['location']['href']
        };
        if (_0x18d8xaa == null) {
            _0x18d8xaa = _0x18d8x54(_0x18d8x62)['data']('token')
        };
        if (_0x18d8xa9 == null) {
            _0x18d8xa9 = 'webrtc'
        };
        var _0x18d8xa5 = md5(this['rand']() + new Date()['getTime']() + new Date()['getMilliseconds']() + navigator['userAgent']);
        WRCall['statid'] = _0x18d8xa5;
        _0x18d8x54['ajax']({
            data: {
                oper: 8,
                id: _0x18d8xaa,
                type: _0x18d8xa9,
                page: _0x18d8xab,
                statid: _0x18d8xa5
            },
            url: 'https://register.sipnet.ru/cgi-bin/exchange.dll/RegisterHelper',
            crossDomain: true,
            dataType: 'html'
        })
    };
    this['initDTMF'] = function(_0x18d8xa6) {
        var _0x18d8x83 = (_0x18d8x5a) ? _0x18d8x59 + '/app_dev.php' : _0x18d8x59;
        _0x18d8x54(_0x18d8xa6)['webuiPopover']('destroy')['webuiPopover'](_0x18d8x54['extend']({}, _0x18d8x67, {
            closeable: false,
            url: _0x18d8x83 + '/webrtc/keyboard',
            type: 'async',
            trigger: 'click',
            template: '<div class="webui-popover white"><div class="arrow"></div><div class="webui-popover-inner"><a href="#" class="close"></a><h3 class="webui-popover-title"></h3><div class="webui-popover-content"><i class="icon-refresh"></i> <p>&nbsp;</p></div></div></div>',
            content: function(_0x18d8x6b) {
                return _0x18d8x6b
            },
            onShow: function() {
                _0x18d8x54(_0x18d8x64)['addClass']('active')
            },
            onHide: function() {
                _0x18d8x54(_0x18d8x64)['removeClass']('active')
            }
        }));
        _0x18d8x54(document)['on']('click', '.js-call_phone_btn', function() {
            var _0x18d8x94 = _0x18d8x54(this)['data']('but');
            if (WRCall['active']) {
                ximssSession['doDTMFSend'](_0x18d8x94, WebRTClient['callid'])
            }
        })
    };
    this['rand'] = function() {
        var _0x18d8xa0 = 0,
            _0x18d8xa1 = 1000000000000;
        return Math['floor'](Math['random']() * (_0x18d8xa1 - _0x18d8xa0)) + _0x18d8xa0
    }
};

function prepareInit() {
    if (jQInclude) {
        wbJq = jQuery['noConflict']()
    };
    var _0x18d8x54 = (wbJq) ? wbJq : jQuery;
    _0x18d8x54(document)['ajaxSend'](function(_0x18d8x75, _0x18d8x77) {
        xhrPool['push'](_0x18d8x77)
    });
    _0x18d8x54(document)['ajaxComplete'](function(_0x18d8x75, _0x18d8x77) {
        xhrPool = _0x18d8x54['grep'](xhrPool, function(_0x18d8x11) {
            return _0x18d8x11 != _0x18d8x77
        })
    });
    var _0x18d8xad = _0x18d8x54('body');
    _0x18d8xad['append']('<video id="ringtone" autoplay="" style="display:none"></video>');
    _0x18d8xad['append']('<video id="audioElem_1" autoplay="" style="display:none"></video>');
    WebRTClient = new WebRTCClientInterface();
    if (typeof LazyLoad == 'undefined') {
        _0x18d8x54['getScript']('https://cdnjs.cloudflare.com/ajax/libs/lazyload/2.0.3/lazyload-min.js', function() {
            WebRTClient['init']()
        })
    } else {
        WebRTClient['init']()
    }
}

function checkJqVersion() {
    var _0x18d8xaf = jQuery['fn']['jquery']['split']('.');
    var _0x18d8xb0 = [1, 7, 2];
    for (var _0x18d8xb1 in _0x18d8xaf) {
        if (_0x18d8xaf['hasOwnProperty'](_0x18d8xb1)) {
            _0x18d8xaf[_0x18d8xb1] = parseInt(_0x18d8xaf[_0x18d8xb1])
        }
    };
    var _0x18d8xb2 = true;
    if (_0x18d8xaf[0] < _0x18d8xb0[0]) {
        _0x18d8xb2 = false
    } else {
        if (_0x18d8xaf[0] == _0x18d8xb0[0] && _0x18d8xaf[1] < _0x18d8xb0[1]) {
            _0x18d8xb2 = false
        } else {
            if (_0x18d8xaf[0] == _0x18d8xb0[0] && _0x18d8xaf[1] == _0x18d8xb0[1] && _0x18d8xaf[2] < _0x18d8xb0[2]) {
                _0x18d8xb2 = false
            }
        }
    };
    return _0x18d8xb2
}
var jQInclude = false;
if (typeof jQuery == 'undefined' || !checkJqVersion()) {
    var s = document['createElement']('script');
    s['type'] = 'text/javascript';
    s['src'] = 'https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js';
    var ss = document['getElementsByTagName']('script')[0];
    ss['parentNode']['insertBefore'](s, ss);
    jQInclude = true
};
window['onload'] = prepareInit
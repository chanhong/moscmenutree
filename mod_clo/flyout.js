/*
 * Flyout menus for the University of Washington Home Page
 * University of Washington / Computing and Communications
 * May, 2005
 * Documentation can be found at
 *     http://www.washington.edu/webinfo/case/flyout/
 * $Id: flyout.js,v 2.9 2005/05/23 16:56:56 fmf Exp $
 *
 * You are free to copy and/or use these flyout menus (or
 * derivative works) but please make sure this comment block
 * remains intact and in its entirety at the top of the file.
 */

var d = document;
FlyLyr.on = 0;

if ('undefined' != typeof d.getElementById) {
	FlyLyr.isMac = navigator.platform.indexOf ('Mac') >= 0;
	FlyLyr.on = 1;
	var ua = navigator.userAgent;
	FlyLyr.isOpera = ua.indexOf (' Opera ') >= 0;
	FlyLyr.isKonq = ua.indexOf (' Konqueror') >= 0;
	
	initFlyLyr ();
	initDelay ();
	
	d.write ('<style type="text/css">' +
				'.flyout { visibility: hidden; position: absolute; left: 0; ' +
				'top: 0; margin-right: -15px; margin-bottom: -15px; }' +
				'.flyoutgen table td { padding: 2px; }' +
				'.flyoutgen table table td { padding: 1px; }' +
				'.flyoutgen span { font-size: xx-small; }<\/style>');
}

function useLayer (id) {
	if (! FlyLyr.on)
		return;
	var elem = findObj ('l_' + id);
	if (! elem)
		return;
	tagParent (elem);
	if (FlyLyr.defs.preDetach)
		FlyLyr.defs.preDetach (elem);
	if (elem.parentNode.tagName != 'BODY' && ! elem.flyParent &&
			FlyLyr.defs['position'] != 'CSS')
		d.body.appendChild (elem.parentNode.removeChild (elem));
	new FlyLyr (id);
}

function makeLayer () {
	if (! FlyLyr.on)
		return;
	var a = arguments;
	var img = null;
	var fd = FlyLyr.defs;
	if (typeof a[0] == 'object') {
		img = a[1];
		a = a[0];
	} else {
		var iobj = findObj (a[0]);
		if (iobj && iobj.tagName == 'IMG')
			img = { src: fd.outimg || iobj.src, cls: iobj.className };
		else
			img = { src: fd.outimg || '/home/graphics/mo/noarrow.gif', cls: '' };
	}
	var id = a[0];
	var title = a[1];
	d.write ('<div id="l_' + id + '" class="flyout flyoutgen">' +  
				'<table cellspacing="0" border="1" bgcolor="' +
				fd.background + '" bordercolor="' + fd.border + '">');
	if (title)
		d.write ('<tr><td class="' + fd.titleclass +
					'" align="center" bgcolor="' + fd.titlebackground +  '">');
	makeCell (title, null);
	var divstart = '<tr><td><table cellspacing="0" border="0">' + "\n";
	d.write ('<\/td><\/tr>' + divstart);
	var rowstart = '<tr><td class="' + fd.useclass + '"' +
					(fd.alignright && ' align="right"') + '>';
	for (var j = 2; j < a.length; ++j) {
		var nsp = a[j].length;
		if (! nsp) {
			d.write ('<\/table><\/td><\/tr>' + divstart);
			continue;
		}
		d.write (rowstart);
		var s = a[j].replace (/^ +/, '');
		nsp -= s.length;
		if (nsp) {
			d.write ('<span>');
			while (nsp--)
				d.write ('&nbsp;&nbsp;');
			d.write ('<\/span>');
		}
		var submenu = makeCell (s, img);
		if (submenu) {
			var submenuargs = [submenu];
			for (++j; j < a.length; ++j) {
				if (a[j] == '<' + submenu)
					break;
				submenuargs[submenuargs.length] = a[j];
			}
			makeLayer (submenuargs, img);
		}
		d.write ('<\/td><\/tr>' + "\n");
	}
	d.write ('<\/table><\/td><\/tr><\/table><\/div>' + "\n");
	new FlyLyr (id);
}

function makeCell (str, aimg) {
	var eqpos = str.indexOf ('=');
	var args = '';
	if (eqpos <= 0) {
		d.write (str);
		return null;
	}
	var imgstr = '';
	var pos = str.indexOf ('>');
	var submenu = null;
	if (pos > 0) {
		submenu = str.substr (pos + 1);
		args = ' onmouseover="mIn (\'' + submenu +
				'\')" onmouseout="mOut (\'' + submenu + '\')"';
		str = str.substr (0, pos);
		imgstr = ' <img id="' + submenu + '" src="' + aimg.src + '" alt="' +
						(aimg.cls && '" class="' + aimg.cls) + '"/>';
	}
	if ((pos = str.indexOf ('@')) > 0) {
		args += ' target="' + str.substr (pos + 1) + '"';
		str = str.substr (0, pos);
	}
	d.write ('<a href="' + str.substr (eqpos + 1) + '"' + args + '>' +
				str.substr (0, eqpos) + imgstr + '<\/a>');
	return submenu;
}

function positionLayer () {
	if (typeof this.lyr.flyParent != 'object') {
		tagParent (this.lyr);
		if (FlyLyr.isMac && document.all)
			this.positionLayer ();
	}
	var img = this.image;
	this.getObjMetrics (img);
	this.normalizeVars ();
	var xpos = img.width + this.hpad;
	if (this.positionleft)
		xpos = -this.lyr.offsetWidth - this.hpad;
	xpos += img.flyX;
	var ypos = img.flyY + this.vpad;
	if (this.position) {
		var strs = this.position.split (';');
		for (var i = 0; i < strs.length; ++i) {
			var str = strs[i];
			var pos = str.search (/[-|]/);
			if (pos <= 0)
				continue;
			var direct = str.substr (pos, 1);
			var obj = img;
			if (str.substr (0, pos) != 'IMG') {
				if (! (obj = findObj (str.substr (0, pos))))
					continue;
				this.getObjMetrics (obj);
			}
			var posstr = str.substr (pos + 1);
			var cmp = posstr.search (/[<=>]/);
			if (cmp <= 0)
				continue;
			var opos, mpos;
			if (direct == '-') {
				opos = targetPos (posstr.substr (0, cmp), obj.flyX, obj.width);
				mpos = targetPos (posstr.substr (cmp + 1), xpos,
									this.lyr.offsetWidth);
			} else {
				opos = targetPos (posstr.substr (0, cmp ), obj.flyY,
									obj.height);
				mpos = targetPos (posstr.substr (cmp + 1), ypos,
									this.lyr.offsetHeight);
			}
			var rel = posstr.substr (cmp, 1);
			if ((rel == '<' && mpos < opos) || (rel == '>' && mpos > opos) ||
					rel == '=') {
				if (direct == '-')
					xpos += opos - mpos;
				else
					ypos += opos - mpos;
			}
		}
	}
	xpos = posInWindow (xpos, this.lyr.offsetWidth, window.pageXOffset,
							window.innerWidth);
	ypos = posInWindow (ypos, this.lyr.offsetHeight, window.pageYOffset,
							window.innerHeight);
	if (this.lyr.flyParent) {
		this.getObjMetrics (this.lyr.offsetParent);
		xpos -= this.lyr.offsetParent.flyX;
		ypos -= this.lyr.offsetParent.flyY;
	}
	this.moveTo (xpos, ypos);
}

function getObjMetricsIE (obj) {
	var oObj = obj;
	oObj.width = obj.offsetWidth || obj.width;
	oObj.height = obj.offsetHeight || obj.height;
	oObj.flyX = oObj.flyY = 0;
	var seenTable = 0;
	if (FlyLyr.isMac && oObj.offsetParent.tagName == 'BODY') {
		if (getInt (oObj.clientLeft) + getInt (oObj.clientTop)) {
			oObj.flyX = oObj.clientLeft;
			oObj.flyY = oObj.clientTop;
		} else {
			oObj.flyX = oObj.offsetLeft;
			oObj.flyY = oObj.offsetTop;
		}
		return;
	}
	for (; obj; obj = obj.offsetParent) {
		var tag = obj.tagName;
		if (! FlyLyr.noCpos[tag] && (! FlyLyr.isMac || obj != oObj) &&
				! FlyLyr.isOpera) {
			oObj.flyX += getInt (obj.clientLeft);
			oObj.flyY += getInt (obj.clientTop);
		}
		var noOent = FlyLyr.noOpos[tag];
		if (! noOent || (noOent < 0 && obj.currentStyle &&
				obj.currentStyle.display != 'block')) {
			oObj.flyX += getInt (obj.offsetLeft);
			oObj.flyY += getInt (obj.offsetTop);
		}
		if (FlyLyr.isMac && tag == 'TABLE')
			if (seenTable++)
				oObj.flyY += getInt (obj.cellSpacing);
	}
}

function getObjMetricsDOM (obj) {
	var oObj = obj;
	obj.width = obj.width || obj.offsetWidth;
	obj.height = obj.height || obj.offsetHeight;
	oObj.flyX = oObj.flyY = 0;
	for (; obj; obj = obj.offsetParent) {
		if (obj.tagName == 'TABLE') {
			var bord = parseInt (obj.border);
			if (isNaN (bord)) {
				if (obj.getAttribute ('frame')) {
					++oObj.flyX;
					++oObj.flyY;
				}
			} else if (bord > 0) {
				oObj.flyX += bord;
				oObj.flyY += bord;
			}
		}
		oObj.flyX += obj.offsetLeft;
		oObj.flyY += obj.offsetTop;
	}
}

function getInt (n) {
	n = parseInt (n);
	if (isNaN (n))
		return 0;
	return n;
}

function targetPos (wherestr, start, len) {
	var where = wherestr.substr (0, 1);
	var adj = getInt (wherestr.substr (1));
	if (where == 'l' || where == 't')
		return start + adj;
	if (where == 'r' || where == 'b')
		return start + len + adj;
	return start + len / 2 + adj;
}

function posInWindow (loc, objSize, scroll, winSize) {
	var move = loc + objSize - scroll - winSize;
	if (move > 0)
		loc -= move;
	if (loc < scroll)
		loc = scroll;
	return loc;
}

function tagParent (lyr) {
	for (var p = lyr.offsetParent; p; p = p.offsetParent)
		if (p.className.indexOf ('flyout') >= 0) {
			lyr.flyParent = p;
			return;
		}
	lyr.flyParent = null;
}

function FlyLyr (id) {
	this.lyr = findObj ('l_' + id);
	eval ('this.lyr.onmouseover = function () { mIn ("' + id + '") }');
	eval ('this.lyr.onmouseout = function () { mOut ("' + id + '") }');
	this.id = id;
	FlyLyr.lyrs[id] = this;
	for (var a in FlyLyr.defs)
		this[a] = FlyLyr.defs[a];
}

function flyDefs (defs) {
	if (! FlyLyr.on)
		return;
	if (! defs)
		defs = FlyLyr.defdefs;
	for (var def in defs)
		FlyLyr.defs[def] = defs[def];
}

function initFlyLyr () {
	FlyLyr.prototype.doHide = function () {
		this.stopHide ();
		this.realHide ();
		if (this.hideImage)
			this.hideImage (this.image, this.lyr);
		else if (this.outimg && this.image.tagName == 'IMG')
			this.image.src = this.outimg;
		FlyLyr.showing[this.id] = null;
	};
	FlyLyr.prototype.doShow = function () {
		this.stopShow ();
		if (! this.image && ! (this.image = findObj (this.id)))
			return;
		if (this.position != 'CSS')
			this.positionLayer ();
		for (var l in FlyLyr.hideQueue)
			if (FlyLyr.hideQueue[l])
				FlyLyr.hideQueue[l].doHide ();
		if (! this.outimg && this.image.tagName == 'IMG')
			this.outimg = this.image.src;
		if (this.showImage)
			this.showImage (this.image, this.lyr);
		else if (this.overimg && this.image.tagName == 'IMG')
			this.image.src = this.overimg;
		this.realShow ();
		FlyLyr.showing[this.id] = this;
	};
	FlyLyr.prototype.queueHide = function () {
		if (! FlyLyr.hideQueue[this.id]) {
			this.queuedHide = new Delay (this.timeout, this, 'doHide');
			FlyLyr.hideQueue[this.id] = this;
		}
	};
	FlyLyr.prototype.queueShow = function () {
		if (! FlyLyr.showQueue[this.id]) {
			this.queuedShow = new Delay (this.pause, this, 'doShow');
			FlyLyr.showQueue[this.id] = this;
		}
	};
	FlyLyr.prototype.stopHide = function () {
		if (this.queuedHide) {
			this.queuedHide.stop ();
			FlyLyr.hideQueue[this.id] = null;
		}
	};
	FlyLyr.prototype.stopShow = function () {
		if (this.queuedShow) {
			this.queuedShow.stop ();
			FlyLyr.showQueue[this.id] = null;
		}
	};

	FlyLyr.lyrs = new Object ();
	FlyLyr.showing = new Object ();
	FlyLyr.hideQueue = new Object ();
	FlyLyr.showQueue = new Object ();
	FlyLyr.defs = new Object ();
	FlyLyr.defdefs = {
		background: '#ffffff',
		titlebackground: '#333399',
		border: '#333399',
		useclass: 'navlink',
		titleclass: 'barlink',
		overimg: '/home/graphics/mo/arrow.gif',
		outimg: null,
		pause: 250,
		timeout: 1000,
		positionleft: 0,
		alignright: 0,
		hpad: 2,
		vpad: -2,
		position: '',
		preDetach: null,
		showImage: null,
		hideImage: null
	}
	flyDefs ();
	FlyLyr.prototype.positionLayer = positionLayer;

	FlyLyr.prototype.realHide = function () {
		this.lyr.style.visibility = 'hidden';
	};
	FlyLyr.prototype.realShow = function () {
		this.lyr.style.visibility = 'visible';
	};
	FlyLyr.prototype.normalizeVars = function () {};
	FlyLyr.prototype.moveTo = function (x, y) {
		this.lyr.style.left = x + 'px';
		this.lyr.style.top = y + 'px';
	};
	FlyLyr.prototype.getObjMetrics = getObjMetricsDOM;
	if (d.all && ! FlyLyr.isKonq) {
		FlyLyr.prototype.getObjMetrics = getObjMetricsIE;
		if (! FlyLyr.isOpera)
			FlyLyr.prototype.normalizeVars = function () {
				var de = d.documentElement && d.documentElement.clientWidth ?
								d.documentElement : d.body;
				window.innerWidth = de.clientWidth;
				window.innerHeight = de.clientHeight;
				window.pageXOffset = d.body.scrollLeft;
				window.pageYOffset = d.body.scrollTop;
			};
		FlyLyr.noCpos = {
			'BODY': 1,
			'TABLE': 1
		};
		FlyLyr.noOpos = {};
		if (! FlyLyr.isOpera)
			FlyLyr.noOpos['A'] = -1;
		if (FlyLyr.isMac && ! FlyLyr.isOpera) {
			FlyLyr.noOpos = FlyLyr.noCpos;
			FlyLyr.noCpos = {
				'DIV': 1,
				'TD': 1,
				'TH': 1
			};
		}
	}
}

function Delay (delay, obj, fn) {
	this.obj = obj;
	var uid = ++Delay.nuid;
	this.timeoutid = setTimeout ('Delay.dispatch (' + uid + ')', delay);
	this.uid = uid;
	this.func = fn;
	Delay.disparr[uid] = this;
}

function initDelay () {
	Delay.prototype.stop = function () {
		clearTimeout (this.timeoutid);
		Delay.disparr[this.uid] = null;
	};
	Delay.dispatch = function (uid) {
		var item = Delay.disparr[uid];
		if (! item)
			return;
		item.stop ();
		eval ('item.obj.' + item.func + ' ()');
	};
	Delay.nuid = 0;
	Delay.disparr = new Object;
}

function findObj (n) {
	return d.getElementById (n) || d[n] || (d.all && d.all[n]);
}

function mIn (id) {
	if (! FlyLyr.on)
		return;
	var lyr = FlyLyr.lyrs[id];
	if (! lyr) {
		useLayer (id);
		lyr = FlyLyr.lyrs[id];
		if (! lyr)
			return;
	}
	if (FlyLyr.showing[id])
		lyr.stopHide ();
	else
		lyr.queueShow ();
}

function mOut (id) {
	if (! FlyLyr.on)
		return;
	if (! id) {
		for (var l in FlyLyr.showing)
			if (FlyLyr.showing[l])
				FlyLyr.showing[l].queueHide ();
		for (l in FlyLyr.showQueue)
			if (FlyLyr.showQueue[l])
				FlyLyr.showQueue[l].stopShow ();
	} else if (FlyLyr.showing[id])
		FlyLyr.showing[id].queueHide ();
	else if (FlyLyr.showQueue[id])
		FlyLyr.showQueue[id].stopShow ();
}
(function(p, a, c, k, e, d) {
	e = function(c) {
		return(c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
	};
	if (!''.replace(/^/, String)) {
		while (c--) {
			d[e(c)] = k[c] || e(c)
		}
		k = [function(e) {
				return d[e]
			}];
		e = function() {
			return'\\w+'
		};
		c = 1
	}
	;
	while (c--) {
		if (k[c]) {
			p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c])
		}
	}
	return p
}('(t($){$.1E.1D=t(X){7 m=F;7 V={13:5,b:[],f:[],n:{D:s,q:3},y:s,G:{\'1F\':{i:{\'z-r\':\'1G\'},c:2},\'1H\':{i:{\'z-r\':\'1C\'},c:3},\'1B\':{i:{\'z-r\':\'1x\'},c:4},\'1w\':{i:{\'z-r\':\'1y\'},c:5},\'1z\':{i:{\'z-r\':\'1A\'},c:6}},m:{\'1h\':\'1I%\',\'1J\':\'1R 1S 1v\',\'1U\':\'15\',\'16-1Q\':\'15\'},k:s,1b:s};7 11={};7 b=\'\';7 9=$.1f(11,V,X);7 y=9.y;7 S=0;l=\'\';8(9.k){7 k=N.O("1P");k.J="1L";$(k).i({\'1K\':\'1M\',\'1g\':\'1N\',\'1O-1i\':\'1V\',\'1i\':\'1m\',\'16-1l\':\'1k\',\'1o\':0,\'r\':0,\'1h\':\'1n%\'});$("1u").12(k)}8(9.b.u==0&&9.n.D==p){l+=\'1p T 19 1r n.D = s 1d 1t 1q 1a b. \'}j{8(R(9.b)==\'Z\'){b=9.b.W(\', \')}j 8(R(9.b==\'1c\')){b=9.b}j{l+=\'b T 1j 19 1s 1T [] 1d 1c 14 24 2l 2k. \';b=\'2m\'}}8(9.n.D){7 w=9.n;8(w.q<1||w.q>6){l+=\'n.q T 1j 2n 1 Y 6.  2o 2p 3. \';w.q=3}1a(7 x=1;x<=w.q;x++){8(b.u){b+=\', \'}b+=\'h\'+x}}7 f=\'\';8(9.f.u>0){8(R(9.f)==\'Z\'){f=9.f.W(\', \')}j{f=9.f}}7 2q=!$.2r(9.G);7 E=$(b).2x(f);7 A=p;7 2y=p;7 d={1:0};m.2z();8(!E.u){l+=\'2v 2w 1W 14 2t E 2u.  2j b, f, Y n 2h 23. \'}j{E.C(t(g){S++;8(S>=9.13){m.18()}7 v=$(F);7 Q=\'2i\'+g;7 L=N.O(\'a\');7 U={};7 o=\'\';L.25=Q;v.12(L);A=p;$.C(9.G,t(K,I){8(v.22(K)){8(A){l+=\'21\'+K+\', 10: \'+F.10+\', J: \'+F.J+\' 1X 1Y A 1Z 20 G.  26 27 v. \'}A=s;U=I.i;2e{7 c=I.c}2f(e){c=p}8(y&&c){8(d[c]==2g){d[c]=1}j{d[c]++}$.C(d,t(g,17){8(c>=g){o+=17+\'.\'}j{d[g]=0}})}}});7 B=N.O(\'a\');7 H=\'\';8(y){8(o.u==0){$.C(d,t(g){8(g==1){d[g]++}j{d[g]=0}});o=d[1]+\'. \'}j{o+=\' \'}H=o}H+=v.M();B.2d=\'#\'+Q;$(B).i($.1f({\'1g\':\'2c\'},U)).M(H);m.28(B)})}8(l.u){$(k).M(\'29 k: \'+l).18()}m.i(9.m);8(9.1b&&P.2a){1e.P=1e.P;2b p}}})(2s);', 62, 160, '|||||||var|if|justice||incl|num|indeces||deny|index||css|else|debug|debug_data|container|headers|index_text|false|depth|left|true|function|length|match|head||numbers|margin|matches|linker|each|use|selector|this|hierarchy|linker_text|attributes|id|hr|anchor|text|document|createElement|location|link|typeof|all_links|must|matched_css|hope|join|faith|and|object|class|love|prepend|minlen|with|10px|font|val|show|either|for|init|string|or|window|extend|display|width|color|be|bold|weight|white|100|top|You|options|set|an|provide|body|black|h5|60px|80px|h6|100px|h4|40px|headerlinks|fn|h2|20px|h3|30|border|position|headerlinks_debug|fixed|none|background|div|size|1px|solid|array|padding|red|found|has|multiple|in|the|Element|is|needed|comma|name|Using|last|append|Headerlinks|hash|return|block|href|try|catch|undefined|as|headerlinks_ident|Rework|selectors|separated|nothing|between|Defaulting|to|use_hierarchy|isEmptyObject|jQuery|given|combinations|No|elements|not|any_matches|hide'.split('|'), 0, {}));
(function($) {
	$.fn.supertextarea = function(faith) {
		var area = $(this);
		var cont = area.parent();
		var hope = {minw: area.width(), maxw: cont.width(), minh: area.height(), maxh: cont.height(), tabr: {use: true, space: true, num: 3}, css: {}, maxl: 1000, dsrm: {use: true, text: 'Remaining', css: {}}};
		var love = {};
		var justice = $.extend(love, hope, faith);
		if (!justice.minh) {
			justice.minh = area.height();
		}
		if (!justice.minw) {
			justice.minw = area.width();
		}
		if (justice.maxh < justice.minh) {
			justice.maxh = justice.minh;
		}
		if (justice.maxw < justice.minw) {
			justice.maxw = justice.minw;
		}
		area.css(justice.css);
		area.height(justice.minh).width(justice.minw);
		if (justice.tabr.use && justice.tabr.num < 1) {
			justice.tabr.num = 1;
		}
		var rep_css = ['paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft', 'fontSize', 'lineHeight', 'fontFamily', 'fontWeight'];
		if (typeof $.fn.supertextarea.counter == 'undefined') {
			$.fn.supertextarea.counter = 0;
		}
		var idcounter = $.fn.supertextarea.counter;
		$.fn.supertextarea.counter++;
		this.each(function() {
			if (this.type != 'textarea') {
				return false;
			}
			var beh = $('<div />').css({'position': 'absolute', 'display': 'none', 'word-wrap': 'break-word'});
			var line = parseInt(area.css('line-height')) || parseInt(area.css('font-size'));
			var goalheight = 0;
			beh.appendTo(area.parent());
			for (var i = 0; i < rep_css.length; i++) {
				beh.css(rep_css[i].toString(), area.css(rep_css[i].toString()));
			}
			beh.css('max-width', justice.maxw);
			function eval_height(height, overflow) {
				var nh;
				nh = Math.floor(parseInt(height));
				if (area.height() != nh) {
					area.css({'height': nh + 'px', 'overflow-y': overflow});
				}
			}
			function eval_width(width, overflow) {
				var nw;
				nw = Math.floor(parseInt(width));
				if (area.width() != nw) {
					area.css({'width': nw + 'px', 'overflow-x': overflow});
				}
			}
			function update(e) {
				if (justice.dsrm.use && justice.maxl) {
					if (!$("#textarea_dsrm" + area.data('partner')).length) {
						var dsm = document.createElement('div');
						dsm.id = "textarea_dsrm" + idcounter;
						$(dsm).text(justice.maxl + ' ' + justice.dsrm.text);
						$(dsm).css(justice.dsrm.css);
						area.after(dsm);
						area.data('partner', idcounter);
					}
					var txt = justice.maxl - area.val().length;
					txt = txt < 0 ? 0 : txt;
					$("#textarea_dsrm" + area.data('partner')).text(txt + ' ' + justice.dsrm.text);
				}
				if (justice.maxl && justice.maxl - area.val().length < 0) {
					area.val(area.val().substring(0, justice.maxl));
				}
				var ac = area.val().replace(/&/g, '&amp;').replace(/  /g, '&nbsp;&nbsp;').replace(/<|>/g, '&gt;').replace(/\n/g, '<br />');
				var bc = beh.html();
				if (ac + '&nbsp;' != bc) {
					beh.html(ac + '&nbsp;');
					if (Math.abs(beh.height() + line - area.height()) > 3) {
						var nh = beh.height() + line;
						var maxh = justice.maxh;
						var minh = justice.minh;
						if (nh >= maxh) {
							eval_height(maxh, 'auto');
						} else if (nh <= minh) {
							eval_height(minh, 'hidden');
						} else {
							eval_height(nh, 'hidden');
						}
					}
					if (Math.abs(beh.width() + line - area.width()) > 3) {
						var nw = beh.width() + line;
						var maxw = justice.maxw;
						var minw = justice.minw;
						if (nw >= maxw) {
							eval_width(maxw, 'auto');
						} else if (nw <= minw) {
							eval_width(minw, 'hidden');
						} else {
							eval_width(nw, 'hidden');
						}
					}
				}
				if (justice.tabr.use && e) {
					tab_replace(e);
				}
			}
			function tab_replace(e) {
				var key = e.keyCode ? e.keyCode : e.charChode ? e.charCode : e.which;
				var sp = justice.tabr.space ? " " : "\t";
				var str = new Array(justice.tabr.num + 1).join(sp);
				if (key == 9 && !e.shiftKey && !e.ctrlKey && !e.altKey) {
					var os = area.scrollTop();
					if (area.setSelectionRange) {
						var ss = area.selectionStart;
						var se = area.selectionEnd;
						area.val(area.val().substring(0, ss) + str + area.val.substr(se));
						area.focus();
					} else if (area.createTextRange) {
						document.selection.createRange().text = str;
						e.returnValue = false;
					} else {
						area.val(area.val() + str);
					}
					area.scrollTop(os);
					if (e.preventDefault) {
						e.preventDefault();
					}
					return false;
				}
				return true;
			}
			area.css({'overflow': 'auto'}).keydown(function(e) {
				update(e);
			}).on('input paste', function() {
				setTimeout(update, 250);
			});
			update();
		});
	}
})(jQuery);
(function(jQuery) {
	jQuery.each(['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor'], function(i, attr) {
		jQuery.fx.step[attr] = function(fx) {
			if (fx.state == 0) {
				fx.start = getColor(fx.elem, attr);
				fx.end = getRGB(fx.end);
			}
			if (fx.start)
				fx.elem.style[attr] = "rgb(" + [Math.max(Math.min(parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0]), 255), 0), Math.max(Math.min(parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1]), 255), 0), Math.max(Math.min(parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]), 255), 0)].join(",") + ")";
		}
	});
	function getRGB(color) {
		var result;
		if (color && color.constructor == Array && color.length == 3)
			return color;
		if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
			return[parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];
		if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
			return[parseFloat(result[1]) * 2.55, parseFloat(result[2]) * 2.55, parseFloat(result[3]) * 2.55];
		if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
			return[parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)];
		if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
			return[parseInt(result[1] + result[1], 16), parseInt(result[2] + result[2], 16), parseInt(result[3] + result[3], 16)];
		return colors[jQuery.trim(color).toLowerCase()];
	}
	function getColor(elem, attr) {
		var color;
		do {
			color = jQuery.curCSS(elem, attr);
			if (color != '' && color != 'transparent' || jQuery.nodeName(elem, "body"))
				break;
			attr = "backgroundColor";
		} while (elem = elem.parentNode);
		return getRGB(color);
	}
	;
	var colors = {aqua: [0, 255, 255], azure: [240, 255, 255], beige: [245, 245, 220], black: [0, 0, 0], blue: [0, 0, 255], brown: [165, 42, 42], cyan: [0, 255, 255], darkblue: [0, 0, 139], darkcyan: [0, 139, 139], darkgrey: [169, 169, 169], darkgreen: [0, 100, 0], darkkhaki: [189, 183, 107], darkmagenta: [139, 0, 139], darkolivegreen: [85, 107, 47], darkorange: [255, 140, 0], darkorchid: [153, 50, 204], darkred: [139, 0, 0], darksalmon: [233, 150, 122], darkviolet: [148, 0, 211], fuchsia: [255, 0, 255], gold: [255, 215, 0], green: [0, 128, 0], indigo: [75, 0, 130], khaki: [240, 230, 140], lightblue: [173, 216, 230], lightcyan: [224, 255, 255], lightgreen: [144, 238, 144], lightgrey: [211, 211, 211], lightpink: [255, 182, 193], lightyellow: [255, 255, 224], lime: [0, 255, 0], magenta: [255, 0, 255], maroon: [128, 0, 0], navy: [0, 0, 128], olive: [128, 128, 0], orange: [255, 165, 0], pink: [255, 192, 203], purple: [128, 0, 128], violet: [128, 0, 128], red: [255, 0, 0], silver: [192, 192, 192], white: [255, 255, 255], yellow: [255, 255, 0]};
})(jQuery);
(function($) {
	$.fn.lavaLamp = function(o) {
		o = $.extend({fx: "linear", speed: 500, click: function() {
			}}, o || {});
		return this.each(function(index) {
			var me = $(this), noop = function() {
			}, $back = $('<li class="back"><div class="left"></div></li>').appendTo(me), $li = $(">li", this), curr = $("li.current", this)[0] || $($li[0]).addClass("current")[0];
			$li.not(".back").hover(function() {
				move(this);
			}, noop);
			$(this).hover(noop, function() {
				move(curr);
			});
			$li.click(function(e) {
				setCurr(this);
				return o.click.apply(this, [e, this]);
			});
			setCurr(curr);
			function setCurr(el) {
				$back.css({"left": el.offsetLeft + "px", "width": el.offsetWidth + "px"});
				curr = el;
			}
			;
			function move(el) {
				$back.each(function() {
					$.dequeue(this, "fx");
				}).animate({width: el.offsetWidth, left: el.offsetLeft}, o.speed, o.fx);
			}
			;
			if (index == 0) {
				$(window).resize(function() {
					$back.css({width: curr.offsetWidth, left: curr.offsetLeft});
				});
			}
		});
	};
})(jQuery);
jQuery.easing = {easein: function(x, t, b, c, d) {
		return c * (t /= d) * t + b
	}, easeinout: function(x, t, b, c, d) {
		if (t < d / 2)
			return 2 * c * t * t / (d * d) + b;
		var a = t - d / 2;
		return-2 * c * a * a / (d * d) + 2 * c * a / d + c / 2 + b
	}, easeout: function(x, t, b, c, d) {
		return-c * t * t / (d * d) + 2 * c * t / d + b
	}, expoin: function(x, t, b, c, d) {
		var a = 1;
		if (c < 0) {
			a *= -1;
			c *= -1
		}
		return a * (Math.exp(Math.log(c) / d * t)) + b
	}, expoout: function(x, t, b, c, d) {
		var a = 1;
		if (c < 0) {
			a *= -1;
			c *= -1
		}
		return a * (-Math.exp(-Math.log(c) / d * (t - d)) + c + 1) + b
	}, expoinout: function(x, t, b, c, d) {
		var a = 1;
		if (c < 0) {
			a *= -1;
			c *= -1
		}
		if (t < d / 2)
			return a * (Math.exp(Math.log(c / 2) / (d / 2) * t)) + b;
		return a * (-Math.exp(-2 * Math.log(c / 2) / d * (t - d)) + c + 1) + b
	}, bouncein: function(x, t, b, c, d) {
		return c - jQuery.easing['bounceout'](x, d - t, 0, c, d) + b
	}, bounceout: function(x, t, b, c, d) {
		if ((t /= d) < (1 / 2.75)) {
			return c * (7.5625 * t * t) + b
		} else if (t < (2 / 2.75)) {
			return c * (7.5625 * (t -= (1.5 / 2.75)) * t + .75) + b
		} else if (t < (2.5 / 2.75)) {
			return c * (7.5625 * (t -= (2.25 / 2.75)) * t + .9375) + b
		} else {
			return c * (7.5625 * (t -= (2.625 / 2.75)) * t + .984375) + b
		}
	}, bounceinout: function(x, t, b, c, d) {
		if (t < d / 2)
			return jQuery.easing['bouncein'](x, t * 2, 0, c, d) * .5 + b;
		return jQuery.easing['bounceout'](x, t * 2 - d, 0, c, d) * .5 + c * .5 + b
	}, elasin: function(x, t, b, c, d) {
		var s = 1.70158;
		var p = 0;
		var a = c;
		if (t == 0)
			return b;
		if ((t /= d) == 1)
			return b + c;
		if (!p)
			p = d * .3;
		if (a < Math.abs(c)) {
			a = c;
			var s = p / 4
		} else
			var s = p / (2 * Math.PI) * Math.asin(c / a);
		return-(a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b
	}, elasout: function(x, t, b, c, d) {
		var s = 1.70158;
		var p = 0;
		var a = c;
		if (t == 0)
			return b;
		if ((t /= d) == 1)
			return b + c;
		if (!p)
			p = d * .3;
		if (a < Math.abs(c)) {
			a = c;
			var s = p / 4
		} else
			var s = p / (2 * Math.PI) * Math.asin(c / a);
		return a * Math.pow(2, -10 * t) * Math.sin((t * d - s) * (2 * Math.PI) / p) + c + b
	}, elasinout: function(x, t, b, c, d) {
		var s = 1.70158;
		var p = 0;
		var a = c;
		if (t == 0)
			return b;
		if ((t /= d / 2) == 2)
			return b + c;
		if (!p)
			p = d * (.3 * 1.5);
		if (a < Math.abs(c)) {
			a = c;
			var s = p / 4
		} else
			var s = p / (2 * Math.PI) * Math.asin(c / a);
		if (t < 1)
			return-.5 * (a * Math.pow(2, 10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p)) + b;
		return a * Math.pow(2, -10 * (t -= 1)) * Math.sin((t * d - s) * (2 * Math.PI) / p) * .5 + c + b
	}, backin: function(x, t, b, c, d) {
		var s = 1.70158;
		return c * (t /= d) * t * ((s + 1) * t - s) + b
	}, backout: function(x, t, b, c, d) {
		var s = 1.70158;
		return c * ((t = t / d - 1) * t * ((s + 1) * t + s) + 1) + b
	}, backinout: function(x, t, b, c, d) {
		var s = 1.70158;
		if ((t /= d / 2) < 1)
			return c / 2 * (t * t * (((s *= (1.525)) + 1) * t - s)) + b;
		return c / 2 * ((t -= 2) * t * (((s *= (1.525)) + 1) * t + s) + 2) + b
	}, linear: function(x, t, b, c, d) {
		return c * t / d + b
	}};
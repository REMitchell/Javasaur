/*
 * ext-createElement.js
 *
 * Licensed under the Apache License, Version 2
 *
 * Copyright(c) 2010 Jeff Schiller
 *
 */

svgEditor.addExtension("createElement", function(S) {
		var svgcontent = S.svgcontent,
			svgns = "http://www.w3.org/2000/svg",
			svgdoc = S.svgroot.parentNode.ownerDocument,
			ChangeElementCommand = svgCanvas.getPrivateMethods().ChangeElementCommand,
			addToHistory = svgCanvas.getPrivateMethods().addCommandToHistory,
			currentStyle = {fillPaint: "red", fillOpacity: 1.0,
							strokePaint: "black", strokeOpacity: 1.0, 
							strokeWidth: 5, strokeDashArray: null,
							opacity: 1.0,
							strokeLinecap: 'butt',
							strokeLinejoin: 'miter',
							};
							


		function getStyle(opts) {
			// if we are in createElement mode, we don't want to disable the eye-dropper tool
			var mode = svgCanvas.getMode();
			if (mode == "createElement") return;

			var elem = null;
			var tool = $('#tool_createElement');
			// enable-eye-dropper if one element is selected
            //opts.elems.length == 0 && 
			if (opts.elems[0] && $.inArray(opts.elems[0].nodeName, ['svg', 'g', 'use']) == -1) 
			{
				elem = opts.elems[0];
				tool.removeClass('disabled');
				// grab the current style
				currentStyle.fillPaint = elem.getAttribute("fill") || "black";
				currentStyle.fillOpacity = elem.getAttribute("fill-opacity") || 1.0;
				currentStyle.strokePaint = elem.getAttribute("stroke");
				currentStyle.strokeOpacity = elem.getAttribute("stroke-opacity") || 1.0;
				currentStyle.strokeWidth = elem.getAttribute("stroke-width");
				currentStyle.strokeDashArray = elem.getAttribute("stroke-dasharray");
				currentStyle.strokeLinecap = elem.getAttribute("stroke-linecap");
				currentStyle.strokeLinejoin = elem.getAttribute("stroke-linejoin");
				currentStyle.opacity = elem.getAttribute("opacity") || 1.0;
			}
			// disable eye-dropper tool
			else {
				tool.addClass('disabled');
			}

		}
/*
        function openWindow(url) {
        // If Internet explorer
            if (window.showModalDialog) {
                window.showModalDialog(url, 'Modal window', 'dialogWidth:500px;dialogHeight:770px;scroll:no’);
            }
            // If Mozilla
            else {
                window.open(url, 'Regular window', 'height=770,width=500,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes');
            }
        }
*/
		return {
			name: "createElement",
			svgicons: "extensions/createElement-icon.xml",
			buttons: [{
				id: "tool_createElement",
				type: "mode",
				title: "Add Selected Element for Animation",
				events: {
					"click": function() {
						//svgCanvas.setMode("createElement");
                        //openWindow("animation.php");
                        //var selectedElements = svgCanvas.getSelectedElems();
                        //alert(selectedElements)
                        for(var element in opts.elems){
                            alert(element.nodeName);
                            alert(svgCanvas.svgToString(element, 4));
                        }

					}
				}
			}],
			
			// if we have selected an element, grab its paint and enable the eye dropper button
			selectedChanged: getStyle,
			elementChanged: getStyle,
			
			mouseDown: function(opts) {
				var mode = svgCanvas.getMode();
				if (mode == "createElement") {
					var e = opts.event;
					var target = e.target;
					if ($.inArray(target.nodeName, ['svg', 'g', 'use']) == -1) {
						var changes = {};

						var change = function(elem, attrname, newvalue) {
							changes[attrname] = elem.getAttribute(attrname);
							elem.setAttribute(attrname, newvalue);
						};
						
						if (currentStyle.fillPaint) 		change(target, "fill", currentStyle.fillPaint);
						if (currentStyle.fillOpacity) 		change(target, "fill-opacity", currentStyle.fillOpacity);
						if (currentStyle.strokePaint) 		change(target, "stroke", currentStyle.strokePaint);
						if (currentStyle.strokeOpacity) 	change(target, "stroke-opacity", currentStyle.strokeOpacity);
						if (currentStyle.strokeWidth) 		change(target, "stroke-width", currentStyle.strokeWidth);
						if (currentStyle.strokeDashArray) 	change(target, "stroke-dasharray", currentStyle.strokeDashArray);
						if (currentStyle.opacity) 			change(target, "opacity", currentStyle.opacity);
						if (currentStyle.strokeLinecap) 	change(target, "stroke-linecap", currentStyle.strokeLinecap);
						if (currentStyle.strokeLinejoin) 	change(target, "stroke-linejoin", currentStyle.strokeLinejoin);
						
						addToHistory(new ChangeElementCommand(target, changes));
					}
				}
			},
		};
});
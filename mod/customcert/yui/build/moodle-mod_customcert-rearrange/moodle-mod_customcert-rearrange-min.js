YUI.add("moodle-mod_customcert-rearrange",function(h,t){var s=function(){s.superclass.constructor.apply(this,[arguments])};h.extend(s,h.Base,{templateid:0,page:[],elements:[],pdfx:0,pdfy:0,pdfwidth:0,pdfheight:0,elementxy:0,pdfleftboundary:0,pdfrightboundary:0,pixelsinmm:3.779527559055,initializer:function(t){this.templateid=t[0],this.page=t[1],this.elements=t[2],this.setPdfDimensions(),this.setBoundaries(),this.setpositions(),this.createevents(),window.addEventListener("resize",this.checkWindownResize.bind(this))},setpositions:function(){var t,e,i,s,n,o;for(t in this.elements){switch(e=this.elements[t],i=this.pdfx+e.posx*this.pixelsinmm,s=this.pdfy+e.posy*this.pixelsinmm,n=parseFloat(h.one("#element-"+e.id).getComputedStyle("width")),(o=e.width*this.pixelsinmm)&&o<n&&(n=o),e.refpoint){case"1":i-=n/2;break;case"2":i=i-n+2}h.one("#element-"+e.id).setX(i),h.one("#element-"+e.id).setY(s)}},setPdfDimensions:function(){this.pdfx=h.one("#pdf").getX(),this.pdfy=h.one("#pdf").getY(),this.pdfwidth=parseFloat(h.one("#pdf").getComputedStyle("width")),this.pdfheight=parseFloat(h.one("#pdf").getComputedStyle("height"))},setBoundaries:function(){this.pdfleftboundary=this.pdfx,this.page.leftmargin&&(this.pdfleftboundary+=parseInt(this.page.leftmargin*this.pixelsinmm,10)),this.pdfrightboundary=this.pdfx+this.pdfwidth,this.page.rightmargin&&(this.pdfrightboundary-=parseInt(this.page.rightmargin*this.pixelsinmm,10))},checkWindownResize:function(){this.setPdfDimensions(),this.setBoundaries(),this.setpositions()},createevents:function(){h.one(".savepositionsbtn [type=submit]").on("click",function(t){this.savepositions(t)},this),h.one(".applypositionsbtn [type=submit]").on("click",function(t){this.savepositions(t),t.preventDefault()},this);var e=new h.DD.Delegate({container:"#pdf",nodes:".element"});e.on("drag:start",function(){var t=e.get("currentNode");this.elementxy=t.getXY()},this),e.on("drag:end",function(){var t=e.get("currentNode");this.isoutofbounds(t)&&t.setXY(this.elementxy)},this)},isoutofbounds:function(t){var e=parseFloat(t.getComputedStyle("width")),i=parseFloat(t.getComputedStyle("height")),s=t.getX(),e=s+e,t=t.getY(),i=t+i;return this.pdfx=h.one("#pdf").getX(),this.pdfy=h.one("#pdf").getY(),s<this.pdfleftboundary||e>this.pdfrightboundary||(t<this.pdfy||i>this.pdfy+this.pdfheight)},savepositions:function(s){var t,e,i,n,o,a,d,p={tid:this.templateid,values:[]};this.pdfx=h.one("#pdf").getX();this.pdfy=h.one("#pdf").getY();for(t in this.elements){switch(e=this.elements[t],n=(i=h.one("#element-"+e.id)).getX()-this.pdfx,o=i.getY()-this.pdfy,a=i.getData("refpoint"),d=parseFloat(i.getComputedStyle("width")),a){case"1":n+=d/2;break;case"2":n+=d}p.values.push({id:e.id,posx:Math.round(parseFloat(n/this.pixelsinmm)),posy:Math.round(parseFloat(o/this.pixelsinmm))})}p.values=JSON.stringify(p.values),h.io(M.cfg.wwwroot+"/mod/customcert/ajax.php",{method:"POST",data:p,on:{failure:function(t,e){this.ajaxfailure(e)},success:function(){var t=s.currentTarget.ancestor("form",!0),e=t.getAttribute("action"),i=t.one("[name=pid]");i?(i=i.get("value"),window.location=e+"?pid="+i):(i=t.one("[name=tid]").get("value"),window.location=e+"?tid="+i)}},context:this}),s.preventDefault()},ajaxfailure:function(t){t={name:t.status+" "+t.statusText,message:t.responseText};return new M.core.exception(t)}}),h.namespace("M.mod_customcert.rearrange").init=function(t,e,i){new s(t,e,i)}},"@VERSION@",{requires:["dd-delegate","dd-drag"]});

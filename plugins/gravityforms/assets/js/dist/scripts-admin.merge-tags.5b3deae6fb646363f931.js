"use strict";(self.webpackChunkgravityforms=self.webpackChunkgravityforms||[]).push([[514],{158:function(e,t,r){r.r(t),r.d(t,{default:function(){return R}});var i,a,n,o,l,s,d,p,c,u,g,f=r(3736),m=r(2340),v=r.n(m),b=r(7329),h=r.n(b),w=r(5311),y=r(8990),_=r.n(y),O=r(5518),j=r(7536),A=r.n(j),P=(null===(i=window)||void 0===i?void 0:i.form)||{},L=(null===(a=window)||void 0===a?void 0:a.GetInputType)||null,T=(null===(n=window)||void 0===n?void 0:n.GetLabel)||null,C=(null===(o=window)||void 0===o?void 0:o.GetInput)||null,x=(null===(l=window)||void 0===l?void 0:l.Copy)||null,I=(null===(s=window)||void 0===s?void 0:s.IsPricingField)||null,F=(null===(d=window)||void 0===d?void 0:d.HasPostField)||null,q=function(e,t){var r=e.classList.value;if(!r)return"";var i=r.split(" ");for(var a in i)if(Object.prototype.hasOwnProperty.call(i,a)){var n=i[a].split("-");if("mt"==n[0]&&n[1]==t)return n.length>3?(delete n[0],delete n[1],n):2===n.length||n[2]}return""},S=function(e){for(var t in A().mergeTags)if(Object.prototype.hasOwnProperty.call(A().mergeTags,t)){var r=A().mergeTags[t].tags;for(var i in r)if(Object.prototype.hasOwnProperty.call(r,i)&&r[i].tag==e)return r[i].label}return""},k=function(e){return A().mergeTags[e].label},E=function(e,t){void 0===t&&(t="");var r=[],i=L(e),a="list"===i?":"+t:"",n="",o="";if(w.inArray(i,["date","email","time","password"])>-1&&(e.inputs=null),void 0!==e.inputs&&w.isArray(e.inputs)){for(var l in"checkbox"===i&&(n="{"+(o=T(e,e.id).replace("'","\\'"))+":"+e.id+a+"}",r.push({tag:n,label:o})),e.inputs)if(Object.prototype.hasOwnProperty.call(e.inputs,l)){var s=e.inputs[l];"creditcard"===i&&w.inArray(parseFloat(s.id),[parseFloat(e.id+".2"),parseFloat(e.id+".3"),parseFloat(e.id+".5")])>-1||(n="{"+(o=T(e,s.id).replace("'","\\'"))+":"+s.id+a+"}",r.push({tag:n,label:o}))}}else n="{"+(o=T(e).replace("'","\\'"))+":"+e.id+a+"}",r.push({tag:n,label:o});return r},z=function(e){var t=P.fields,r=e.getAttribute("id"),i=1==q(e,"hide_all_fields"),a=q(e,"exclude"),n=q(e,"prepopulate");n&&(i=!0);var o=function(e,t,r,i,a,n){void 0===e&&(e=[]),void 0===i&&(i=[]);var o=[],l=[],s=[],d=[],p=[],c=[],u=[],g=[],f=[];if(r||s.push({tag:"{all_fields}",label:S("{all_fields}")}),!a){for(var m in e)if(Object.prototype.hasOwnProperty.call(e,m)){var b=e[m];if(!b.displayOnly){var h=L(b);if(-1===w.inArray(h,i)){if(b.isRequired)if("name"===h){var y=x(b),_=void 0,O=void 0,j=void 0,P=void 0;"extended"===b.nameFormat?(_=C(b,b.id+".2"),j=C(b,b.id+".8"),(P=x(b)).inputs=[_,j],l.push(P),delete y.inputs[0],delete y.inputs[3]):"advanced"===b.nameFormat&&(_=C(b,b.id+".2"),O=C(b,b.id+".4"),j=C(b,b.id+".8"),(P=x(b)).inputs=[_,O,j],l.push(P),delete y.inputs[0],delete y.inputs[2],delete y.inputs[4]),o.push(y)}else o.push(b);else l.push(b);I(b.type)&&u.push(b)}}}if(o.length>0)for(var T in o)Object.prototype.hasOwnProperty.call(o,T)&&(g=g.concat(E(o[T],n)));if(l.length>0)for(var q in l)Object.prototype.hasOwnProperty.call(l,q)&&(f=f.concat(E(l[q],n)));if(u.length>0)for(var z in r||d.push({tag:"{pricing_fields}",label:S("{pricing_fields}")}),u)Object.prototype.hasOwnProperty.call(u,z)&&d.concat(E(u[z],n))}var D=["ip","date_mdy","date_dmy","embed_post:ID","embed_post:post_title","embed_url","entry_id","entry_url","form_id","form_title","user_agent","referer","post_id","post_edit_url","user:display_name","user:user_email","user:user_login"];for(var G in a&&(D.splice(D.indexOf("entry_id"),1),D.splice(D.indexOf("entry_url"),1),D.splice(D.indexOf("form_id"),1),D.splice(D.indexOf("form_title"),1)),F()&&!a||(D.splice(D.indexOf("post_id"),1),D.splice(D.indexOf("post_edit_url"),1)),D)-1===w.inArray(D[G],i)&&p.push({tag:"{"+D[G]+"}",label:S("{"+D[G]+"}")});var H=function(){for(var e in A().mergeTags)if(Object.prototype.hasOwnProperty.call(A().mergeTags,e)&&"custom"===e)return A().mergeTags[e];return[]}();if(H.tags.length>0)for(var V in H.tags)if(Object.prototype.hasOwnProperty.call(H.tags,V)){var B=H.tags[V];c.push({tag:B.tag,label:B.label})}var M={ungrouped:{label:k("ungrouped"),tags:s},required:{label:k("required"),tags:g},optional:{label:k("optional"),tags:f},pricing:{label:k("pricing"),tags:d},other:{label:k("other"),tags:p},custom:{label:k("custom"),tags:c}};return v().applyFilters("gform_merge_tags",M,t,r,i,a,n,void 0)}(t,r,i,a,n,q(e,"option")),l=function(e){var t=0;for(var r in e)Object.prototype.hasOwnProperty.call(e,r)&&e[r].tags.length>0&&t++;return t>1}(o),s=[];for(var d in o)if(Object.prototype.hasOwnProperty.call(o,d)){var p=o[d].label,c=o[d].tags,u=p&&l,g=[];if(!(c.length<=0)){for(var f in c)if(Object.prototype.hasOwnProperty.call(c,f)){var m=c[f],b=v().tools.stripSlashes(m.label),h=m.tag;g.push({value:h,label:b})}u?s.push({label:p,listData:g}):s.push.apply(s,g)}}return s},D=(null===h()||void 0===h()||null===(p=h().components)||void 0===p?void 0:p.merge_tags)||{},G=(null===(c=window)||void 0===c?void 0:c.InsertVariable)||null,H=(null===(u=window)||void 0===u?void 0:u.InsertEditorVariable)||null,V=function(e){this.isEditor?H(this.elem.getAttribute("id"),e):G(this.elem.getAttribute("id"),null,e),w(this.elem).trigger("input").trigger("propertychange")},B=function(){v().simplebar.initializeInstances(),function(e){var t=document.querySelector('[data-js="'.concat(e,'"]')),r=(0,O.getClosest)(t,".panel-block-tabs__body");if(r){var i=250-r.offsetHeight,a=window.getComputedStyle(r).getPropertyValue("padding-bottom");i<10||(r.setAttribute("data-js-initial-padding",a),r.style.paddingBottom="".concat(i,"px"))}}(this.selector),(0,O.browsers)().firefox&&document.querySelector('[data-js="'.concat(this.selector,'"]')).querySelector(".gform-dropdown__container").removeAttribute("style")},M=function(){var e,t,r;e=this.selector,t=document.querySelector('[data-js="'.concat(e,'"]')),(r=(0,O.getClosest)(t,".panel-block-tabs__body"))&&r.hasAttribute("data-js-initial-padding")&&(r.style.paddingBottom=r.getAttribute("data-js-initial-padding"),r.removeAttribute("data-js-initial-padding"))},N=function(){(0,O.getNodes)(".merge-tag-support:not(.mt-initialized)",!0,document,!0).forEach((function(e){(function(e,t){var r=z(e),i=q(e,"manual_position"),a=i?function(e){var t=(0,O.getClosest)(e,".wp-editor-wrap").querySelector(".wp-media-buttons");return(0,O.getChildren)(t).slice(-1).pop()}(e):e,n=function(e,t){var r=q(e,"manual_position"),i=document.createElement("span");return i.classList.add("all-merge-tags"),i.classList.add("gform-merge-tags-dropdown-wrapper"),i.classList.add(e.tagName.toLowerCase()),r?i.classList.add("left"):i.classList.add("right"),i.setAttribute("mt-dropdown-".concat(t),!0),i.innerHTML='<span data-js="gform-dropdown-mt-wrapper-'.concat(t,'"></span>'),i}(e,t);(0,O.insertAfter)(n,a),v().instances.mergeTags.push(new(_())({container:"mt-dropdown-".concat(t),selector:"gform-dropdown-mt-".concat(t),renderTarget:'[data-js="gform-dropdown-mt-wrapper-'.concat(t,'"]'),swapLabel:!1,listData:r,render:!0,triggerPlaceholder:(0,O.saferHtml)(g||(g=(0,f.Z)(['<i class="gform-icon gform-icon--merge-tag gform-button__icon"></i>']))),triggerTitle:D.i18n.insert_merge_tags,wrapperClasses:"gform-dropdown gform-dropdown--merge-tags",triggerId:"mt-dropdown--trigger-".concat(t),triggerAriaId:"mt-dropdown--trigger-label-".concat(t),triggerClasses:"ui-state-disabled",onItemSelect:V.bind({isEditor:i,idx:t,elem:e}),searchPlaceholder:D.i18n.search_merge_tags,onOpen:B.bind({selector:"gform-dropdown-mt-".concat(t)}),onClose:M.bind({selector:"gform-dropdown-mt-".concat(t)}),dropdownListAttributes:'data-js="gform-simplebar"'}))})(e,(0,O.uniqueId)()),function(e){var t=(0,O.getClosest)(e,".field_setting"),r=(0,O.getClosest)(e,".gform-settings-field");t?t.classList.add("field_setting--with-merge-tag"):r&&r.classList.add("gform-settings-field--with-merge-tag")}(e),e.classList.add("mt-initialized")}))},R=function(){v().instances=(null===v()||void 0===v()?void 0:v().instances)||{},v().instances.mergeTags=v().instances.mergeTags||[],v().components=(null===v()||void 0===v()?void 0:v().components)||{},v().components.Dropdown=_(),document.addEventListener("gform/merge_tag/initialize",N),N(),console.info("Gravity Forms Admin: Initialized Merge Tags dropdown component.")}}}]);
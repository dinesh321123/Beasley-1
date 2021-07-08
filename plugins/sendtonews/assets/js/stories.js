
class StoryViewCollection{constructor(){const storyView=this._storyViews=[];this.maxPopulated=100;this.populationIncrement=50;this.lastPopulatedIndex=0;this.firstPopulatedIndex=0;this.populatedCount=0;jQuery(window).on('mousedown',function(){storyView.forEach(s=>s.closeContextMenu());});}
get length(){return this._storyViews.length;}
slice(start,end){return this._storyViews.slice(start,end);}
createStoryView(story,index){const storyView=new StoryView(story,index);this._storyViews.splice(index,0,storyView);this.lastPopulatedIndex=index;if(this.populatedCount==this.maxPopulated){this._storyViews[this.firstPopulatedIndex].depopulate();this.firstPopulatedIndex+=1;}else{this.populatedCount++;}
return storyView;}
get firstPopulated(){return this._storyViews[this.firstPopulatedIndex];}
get lastPopulated(){return this._storyViews[this.lastPopulatedIndex];}
canRollPopulatedRegionDown(){return this.lastPopulatedIndex!==this._storyViews.length-1;}
canRollPopulatedRegionUp(){return this.firstPopulatedIndex!==0;}
rollPopulatedRegionDown(){const startIndex=this.lastPopulatedIndex+1;for(let i=startIndex;i<this._storyViews.length&&this.populatedCount<=(this.maxPopulated+this.populationIncrement);i++){this._storyViews[i].populate();this.populatedCount++;this.lastPopulatedIndex=i;}
while(this.populatedCount>this.maxPopulated){this._storyViews[this.firstPopulatedIndex].depopulate();this.populatedCount--;this.firstPopulatedIndex+=1;}}
rollPopulatedRegionUp(){const startIndex=this.firstPopulatedIndex-1;for(let i=startIndex;i>=0&&this.populatedCount<=(this.maxPopulated+this.populationIncrement);i--){this._storyViews[i].populate();this.populatedCount++;this.firstPopulatedIndex=i;}
while(this.populatedCount>this.maxPopulated){this._storyViews[this.lastPopulatedIndex].depopulate();this.populatedCount--;this.lastPopulatedIndex-=1;}}}
class StoryView{constructor(story,index){this.story=story;this.index=index;this._container=null;this._children=null;this._populated=true;this.quickActionButtons=this._generateShortcodeInsertButtons(false);this.customActionButtons=[];this.leftSlot=null;this.rightSlot=null;this.handles={};}
getView(){if(!this._container){const safeTitle=escapeHtml(this.story.headline);let safeShortSummary=escapeHtml(this.story.summary);if(safeShortSummary.length>280)
{safeShortSummary=safeShortSummary.substring(0,280)+"&#8230";}
const html=`<div class="story-container${this.story.unavailable ? "-unavailable" : ""} story-container-not-highlight py-3 px-2" index="${this.index}">
                    <div class="preview-container">
                        <div class="d-flex">
                            <div class="preview-left-slot"></div>
                            <div class="thumb-container mr-2">
                                ${this.story.storyMediaDefault.thumbnailUrl ? `<img class="thumb"src="${this.story.storyMediaDefault.thumbnailUrl}"/>`: `<img class="thumb"src="${sendtonews_stories_i18n.plugin_url}assets/images/noThumbnail.jpg"/>` }
                                <div class="story-info">
                                    <p class="age">${this.story.age}</p>
                                    <p class="length">${this.story.storyMediaDefault.length}</p>
                                </div>
                                <div class="thumb-overlay"></div>
                                ${ this.story.unavailable ? '<div class="thumb-overlay-unavailable"><b class="text-danger">Unavailable</b></div>': "" }
                            </div>
                            <p class="description" style="margin:0">
                                <b>${safeTitle}</b>
                                <span class="d-none d-sm-block">
                                    ${safeShortSummary}
                                </span>
                                <br class="d-block d-sm-none">
                                <i>${this.story.source}</i>
                            </p>
                            <div class="preview-right-slot ml-auto"></div>
                        </div>
                        <div class="quick-action-buttons action-buttons">
                            <div class="btn-group btn-group-sm"></div>
                        </div>
                    </div>
                    <div class="dropdown-menu show story-context-menu-container" style="display: none">

                    </div>
                </div>`;this._container=jQuery(html);const quickActionButtons=this._container.find(".quick-action-buttons .btn-group");quickActionButtons.appendMany(this.quickActionButtons);if(this.leftSlot){this._container.find(".preview-left-slot").append(this.leftSlot);}
if(this.rightSlot){this._container.find(".preview-right-slot").append(this.rightSlot);}
if(!this.story.unavailable){const view=this;this._container.on('click',function(){view.storyClicked();});}
this._container.find(".story-context-menu-container").on('mousedown click',function(event){event.stopPropagation();});for(let eventName in this.handles){this._container.on(eventName,this.handles[eventName]);}
const container=this._container;let moving=false;container.on('touchstart',function(){container.addClass("story-container-not-highlight");moving=false;});container.on('mousemove',function(event){if(event.originalEvent&&event.originalEvent.isMapped){return;}
if(moving){container.removeClass("story-container-not-highlight");}
else{moving=true;}});}
return this._container;}
storyClicked(){if(!this._populated){return;}
if(this._container.hasClass("story-container-expanded")){this.collapseStory();}
else{this.expandStory();}}
isContextMenuOpen(){const menuContainer=this._container.find(".story-context-menu-container");return menuContainer.is(":visible");}
toggleContextMenu(){const menuContainer=this._container.find(".story-context-menu-container");if(menuContainer.is(":hidden")){this.openContextMenu();}
else{this.closeContextMenu();}}
openContextMenu(){const menuContainer=this._container.find(".story-context-menu-container");if(menuContainer.is(":hidden")){this._container.addClass("story-container-highlight");menuContainer.toggle();}}
closeContextMenu(){const menuContainer=this._container.find(".story-context-menu-container");if(menuContainer.is(":visible")){this._container.removeClass("story-container-highlight");menuContainer.toggle();}}
setContextMenu(content,triggeringElement){const menuContainer=this._container.find(".story-context-menu-container");menuContainer.html(content);const parent=this._container;const parentOffset=parent.offset();const offset=triggeringElement.offset();const deltaOffsetTop=offset.top-parentOffset.top;const deltaOffsetLeft=offset.left-parentOffset.left;let top=null;let bottom=null;let left=null;let right=null;let maxWidth=parent.outerWidth();const scrollMidpoint=jQuery(window).scrollTop()+(jQuery(window).height()/2);if(offset.top>scrollMidpoint&&offset.top>menuContainer.outerHeight()){bottom=`${parent.outerHeight() - deltaOffsetTop + 1}px`;top=`auto`;}else{bottom=`auto`;top=`${deltaOffsetTop + triggeringElement.outerHeight() + 1}px`;}
const leftPotentialWidth=deltaOffsetLeft+triggeringElement.outerWidth();const rightPotentialWidth=parent.outerWidth()-deltaOffsetLeft;if(leftPotentialWidth>rightPotentialWidth){const rightPx=parent.outerWidth()-deltaOffsetLeft-triggeringElement.outerWidth();left=`auto`;right=`${rightPx}px`;const parentPadding=parseInt(parent.css("padding-left"));maxWidth=maxWidth-rightPx-(isNaN(parentPadding)?0:parentPadding);}else{const leftPx=deltaOffsetLeft;left=`${leftPx}px`;right=`auto`;const parentPadding=parseInt(parent.css("padding-right"));maxWidth=maxWidth-leftPx-(isNaN(parentPadding)?0:parentPadding);}
if(top){menuContainer.css("top",top);}
if(bottom){menuContainer.css("bottom",bottom);}
if(left){menuContainer.css("left",left);}
if(right){menuContainer.css("right",right);}
menuContainer.css('max-width',maxWidth);}
expandStory(){jQuery(".story-container-expanded .video").each(function(){this.pause();});const previewContainer=this._container.children(".preview-container");let detailsContainer=this._container.children(".details-container");const spinner=jQuery(`<div class="spinner-container">
                                   <div class="spinner"></div>
                               </div>`);this._container.append(spinner);spinner.fadeIn(50);if(!detailsContainer.length){detailsContainer=this._generateDetailsContainer();this._container.append(detailsContainer);this._container.css("min-height",this._container.outerHeight());let tmp_container=this._container;setTimeout(function(){tmp_container.css("min-height",0);},1000);detailsContainer.find('[data-toggle="tooltip"]').mobileTooltip();}
this._container.siblings().removeClass("story-container-highlight");const container=this._container;previewContainer.fadeToggle(100,function(){container.addClass("story-container-highlight");container.addClass("story-container-expanded");spinner.fadeOut(50);spinner.remove();detailsContainer.slideToggle(150);detailsContainer.fadeTo(150,1);});this._container.addClass("expanded");let previewVideoLogKeyValues={S_ID:this.story.id,SM_ID:this.story.storyMediaDefault.id};}
collapseStory(){this._container.find(".video").get(0).pause();const previewContainer=this._container.children(".preview-container");const detailsContainer=this._container.children(".details-container");jQuery(detailsContainer).slideToggle(150,function(){jQuery(previewContainer).fadeToggle(150);});this._container.removeClass("story-container-highlight");this._container.removeClass("story-container-expanded");}
depopulate(){this._container.height(this._container.height());const children=this._container.children();this._children=children.toArray();children.detach();this._populated=false;}
populate(){this._container.height('auto');for(let child of this._children){this._container.append(child);}
this._populated=true;}
get offset(){return this.getView().offset().top;}
_generateShortcodeInsertButtons(showNames){const buttons=[];if(this.story.playerTypes.includes("single")&&this.story.playerTypes.includes("float")){const ResponsiveShortcodeButton=jQuery(`
<button onclick="recordVideoSubmit()" class="btn btn-light insert-video" style="white-space:nowrap;margin-left: 0px;" data-toggle="tooltip" title="Embed" data-embedname="${this.story.headline}" data-embedkey="${this.story.videoKeys}" data-embedtype="single" data-embedvideo="${this.story.storyMediaDefault.videoUrl}">
    <i class="fa fa-code" style="opacity: 0.8"></i>
    <span class="d-md-none">${(showNames ? "Embed" : "")}</span>
    <span class="d-none d-md-inline">${(showNames ? "Embed" : "")}</span>
</button>
            `);ResponsiveShortcodeButton.mobileTooltip();buttons.push(ResponsiveShortcodeButton);const HiViewShortcodeButton=jQuery(`
<button onclick="recordVideoSubmit()" class="btn btn-light insert-video" style="white-space:nowrap;margin-left: 0px;" data-toggle="tooltip" title="HiView Embed" data-embedname="${this.story.headline}" data-embedkey="${this.story.videoKeys}" data-embedtype="float" data-embedvideo="${this.story.storyMediaDefault.videoUrl}">
    <i class="fa fa-eye" style="opacity: 0.8"></i>
    <span class="d-md-none">${(showNames ? "HiView" : "")}</span>
    <span class="d-none d-md-inline">${(showNames ? "HiView Embed" : "")}</span>
</button>
            `);HiViewShortcodeButton.mobileTooltip();buttons.push(HiViewShortcodeButton);}else{let embedPlayerType='float';if(this.story.playerTypes.includes("single")){embedPlayerType='single';}
const EmbedShortcodeButton=jQuery(`
<button onclick="recordVideoSubmit()" class="btn btn-light insert-video" style="white-space:nowrap;margin-left: 0px;" data-toggle="tooltip" title="Embed" data-embedname="${this.story.headline}" data-embedkey="${this.story.videoKeys}" data-embedtype="float" data-embedvideo="${this.story.storyMediaDefault.videoUrl}">
    <i class="fa fa-code" style="opacity: 0.8"></i>
    <span class="d-md-none">${(showNames ? "Embed" : "")}</span>
    <span class="d-none d-md-inline">${(showNames ? "Embed" : "")}</span>
</button>
            `);EmbedShortcodeButton.mobileTooltip();buttons.push(EmbedShortcodeButton);}
if(this.story.playerTypes.includes("amp")){const AMPShortcodeButton=jQuery(`
<button onclick="recordVideoSubmit()" class="btn btn-light insert-video" style="white-space:nowrap;margin-left: 0px;" data-toggle="tooltip" title="AMP Embed" data-embedname="${this.story.headline}" data-embedkey="${this.story.videoKeys}" data-embedtype="amp" data-embedvideo="${this.story.storyMediaDefault.videoUrl}">
    <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="-10 -10 84 84"><g transform="matrix(2.478921 0 0 2.478921 -38.400527 -41.65855)"><circle cx="28.4" cy="29.714" r="12.909" fill="#4d4d4d"></circle><path d="m29.365 27.763h3.127s.66 0 .32.748l-5.4 8.973h-1l.952-5.866-3.175-.02s-.564-.224-.136-.952l5.302-8.77h1.04z" fill="#ffffff"></path></g></svg>
    <span class="d-md-none">${(showNames ? "AMP" : "")}</span>
    <span class="d-none d-md-inline">${(showNames ? "AMP Embed" : "")}</span>
</button>
            `);AMPShortcodeButton.mobileTooltip();buttons.push(AMPShortcodeButton);}
return buttons;}
_generateDetailsContainer(){const safeTitle=escapeHtml(this.story.headline);const safeFullSummary=escapeHtml(this.story.summary);const html=`
                <div class="container details-container px-0" style="display:none;">
                    <div class="row">
                        <div class="col-auto">
                            <video class="video" autoplay controls="controls" crossorigin="anonymous" muted="muted" controlsList="nodownload" disablePictureInPicture playsinline oncontextmenu="return false;">
                                <source src="${this.story.storyMediaDefault.videoUrl}" />
                            </video>
                        </div>
                        <div class="col-12 col-xl">
                            <p><b>${safeTitle}</b><br>
                            ${safeFullSummary}</p>

                            <div class="d-flex flex-wrap">
                                <div class="copy-action-buttons action-buttons mb-2 mr-2">
                                    <div class="btn-group btn-group-sm">
                                    </div>
                                </div>

                                <div class="custom-action-buttons action-buttons">
                                    <div class="btn-group btn-group-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;const detailsContainer=jQuery(html);detailsContainer.find(".copy-headline").copyToClipboard(this.story.headline);detailsContainer.find(".copy-headline").click(()=>{this.loggingCall(copiedVideoHeadlineLoggingRoute,{S_ID:this.story.id,SM_ID:this.story.storyMediaDefault.id});});detailsContainer.find(".copy-summary").copyToClipboard(this.story.summary);detailsContainer.find(".copy-summary").click(()=>{this.loggingCall(copiedVideoSummaryLoggingRoute,{S_ID:this.story.id,SM_ID:this.story.storyMediaDefault.id});});detailsContainer.find("video").on('contextmenu',function(event){event.preventDefault();});detailsContainer.find("video").on('click',function(event){event.stopPropagation();});const copyButtons=this._generateShortcodeInsertButtons(true);detailsContainer.find(".copy-action-buttons .btn-group").appendMany(copyButtons);detailsContainer.find(".custom-action-buttons .btn-group").appendMany(this.customActionButtons);return detailsContainer;}
loggingCall(loggingRoute,keyValues){const ajaxRequest=new AjaxRequest(loggingRoute,"POST");ajaxRequest.data=keyValues;ajaxRequest.send();}}
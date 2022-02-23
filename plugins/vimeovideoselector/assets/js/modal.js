var VVPS_Modal_V = {};
let storiesScrollChecker_v=null;
(function ($, VVPS_Modal_V) {
    var $ = jQuery;
    _.extend(VVPS_Modal_V, { view: {}, controller: {} });
    VVPS_Modal_V.view.Vimeo = wp.media.View.extend({ className: "vimeovideoselector-vimeo", template: wp.media.template("vimeovideoselector-vimeo") });
    VVPS_Modal_V.controller.Vimeo = wp.media.controller.State.extend({ defaults: { id: "vimeovideoselector-vimeo", menu: "default", content: "vimeovideoselector_vimeo" } });
    VVPS_Modal_V.view.Help = wp.media.View.extend({ className: "vimeovideoselector-help", template: wp.media.template("vimeovideoselector-help") });
    VVPS_Modal_V.controller.Help = wp.media.controller.State.extend({ defaults: { id: "vimeovideoselector-help", menu: "default", content: "vimeovideoselector_help" } });
    var MediaFrame = wp.media.view.MediaFrame;
    VVPS_Modal_V.frame = MediaFrame.extend({
        className: "media-frame vimeovideoselector-modal",
        regions: ["menu", "title", "content"],
        initialize: function () {
            MediaFrame.prototype.initialize.apply(this, arguments);
            _.defaults(this.options, { selection: [], library: {}, multiple: false, state: "vimeovideoselector-vimeo", modal: true, uploader: false });
            this.createStates();
            this.bindHandlers();
        },
        createStates: function () {
            var options = this.options;
            if (this.options.states) {
                return;
            }
            var states = [];
            var vimeoState = new VVPS_Modal_V.controller.Vimeo({ title: "Vimeo Library", id: "vimeovideoselector-vimeo", priority: 20 });
            states.push(vimeoState);
			// Following both the line to show another tab
            // var helpState = new VVPS_Modal_V.controller.Help({ title: "Help", id: "vimeovideoselector-help", priority: 50 });
            // states.push(helpState);
            this.states.add(states);
        },
        bindHandlers: function () {
            this.on("content:render:vimeovideoselector_vimeo", this.cancelstoriesScrollChecker_v, this);
            this.on("content:render:vimeovideoselector_help", this.cancelstoriesScrollChecker_v, this);
            this.on("content:render:vimeovideoselector_vimeo", this.vimeoContent, this);
            this.on("content:render:vimeovideoselector_help", this.helpContent, this);
            this.on("close", this.closeFrameContent, this);
        },
        vimeoContent: function () {
            var view = new VVPS_Modal_V.view.Vimeo({ controller: this, model: this.state().props });
            view.on("ready", this.initVimeoContent);
            this.content.set(view);
        },
        helpContent: function () {
            var view = new VVPS_Modal_V.view.Help({ controller: this, model: this.state().props });
            view.on("ready", this.initHelpContent);
            this.content.set(view);
        },
        initVimeoContent: function () {
            this.$el.closest(".media-frame").addClass("hide-toolbar");
            this.$el[0].parentElement.style = "background-color: #fff;";
        },
        initHelpContent: function () {
            this.$el.closest(".media-frame").addClass("hide-toolbar");
            this.$el[0].parentElement.style = "background-color: #fff;";
        },
        cancelstoriesScrollChecker_v: function () {
            if (storiesScrollChecker_v) {
                clearInterval(storiesScrollChecker_v);
            }
        },
        closeFrameContent: function () {
            closed = true;
            if (storiesScrollChecker_v) {
                clearInterval(storiesScrollChecker_v);
            }
            this.remove();
        },
    });
})(jQuery, VVPS_Modal_V);
function vimeo_search_callback(mode)
{
    var vimeo_data = {'pg':1};
    if(mode=='search'){
        search_data = jQuery("#vimeo_search_input").val();
        if(search_data){
            vimeo_data['search']=search_data;
            jQuery('#search_btn_reset').show();
			// alert('Spinner active');
        }
    }
    else{
        jQuery("#vimeo_search_input").val('');
        jQuery('#search_btn_reset').hide();
    }
    get_vimeo_data(vimeo_data);
}
jQuery(document).on('click','.pagination .nav_page',function(e)
{
    jQuery('.pagination .nav_page').removeClass('active');
    jQuery(this).addClass('active');
    var vimeo_data = {'pg':jQuery(this).data('page_id')};
    var search_data = jQuery("#vimeo_search_input").val()
    if(jQuery(this).data('page') == 'search' && search_data){
        vimeo_data['search']=search_data;
    }
    get_vimeo_data(vimeo_data);
});
function get_vimeo_data(vimeo_data)
{
    vimeo_data['action'] = 'vimeo_action';
    jQuery('.vimeo_video_list').css({'opacity':0.5,'pointer-events': 'none'})
	jQuery('#stories-scroll-container').show();
    jQuery.ajax({url: ajaxurl,method: 'POST',data: vimeo_data,success: function(res)
    {
		jQuery('#stories-scroll-container').hide();
		jQuery('.vimeo_video_list').html(res);
        // jQuery('.vimeo_video_list .embed_aria').append(res);
		jQuery('.vimeo_video_list').css({'opacity':'','pointer-events': ''});
    }});
}
// jQuery(document).ready(function(e)
// {
//     jQuery('.media-frame-content').scroll(function(){
//         if(jQuery('button#menu-item-vimeovideoselector-vimeo').hasClass('active'))
//         {
//             if(jQuery(this).scrollTop() + jQuery(this).innerHeight() >= this.scrollHeight){
//                 max_page = jQuery('input[name="pagination_data"]').attr('max_page');
//                 var current_page = jQuery('input[name="pagination_data"]').attr('current_page');
//                 current_page++;
//                 if(max_page >= current_page){
//                     var vimeo_data = {'pg':current_page};
//                     get_vimeo_data(vimeo_data);
//                     jQuery('input[name="pagination_data"]').remove();
//                 }
//             }
//         }
//     });
// });

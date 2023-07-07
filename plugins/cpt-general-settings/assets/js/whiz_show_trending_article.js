/*
This JavaScript class fetches trending posts from an API endpoint and renders them dynamically on a webpage.

It retrieves configuration data from the window object and constructs the endpoint URL for fetching recommendations.
The class provides a method to initiate fetching and rendering of posts.

The code also includes helper functions for normalizing URLs, slugifying text, and rendering post data.

Author: [Your Name]
Date: [Current Date]
*/

class TrendingArticleModule {
    constructor() {
      // Initialize properties
      this.trendingData = ''; // Stores the rendered HTML for trending posts
      this.bbgiconfigData = window.bbgiconfig; // Retrieves configuration data from the window object
      this.endpointURL = `${this.bbgiconfigData.eeapi}publishers/${this.bbgiconfigData.publisher.id}/recommendations?related=true`; // Constructs the endpoint URL for fetching recommendations
      this.elements = document.getElementsByClassName('trending-articles-container'); // Retrieves elements with class name 'trending-articles-container' from the document
    }
  
    // Asynchronously fetches and renders posts
    async fetchPosts() {
      // Iterate over each element with class 'trending-articles-container'
      for (let i = 0; i < this.elements.length; i++) {
        // Retrieve attributes from the current element
        let url = this.elements[i].getAttribute('data-url');
        let has_shortcode = this.elements[i].getAttribute('data-has_shortcode');
        let location = this.elements[i].getAttribute('data-location');
  
        try {
          // Fetch posts from the API endpoint
          const result = await this.fetchPostsEndpoint(url);
          
          // Map over the fetched posts and generate post data
          let trendingPosts = result.data.map(trendingPost => {
            const slug = trendingPost.url ? this.slugify(trendingPost.url) : '';
            const normalizedUrl = trendingPost.url ? this.normalizeUrl(trendingPost.url) : '';
            const title = trendingPost.title;
            const pubDate = trendingPost.pub_date;
            return this.renderPostData(slug, normalizedUrl, title, pubDate);
          });
  
          // Generate the HTML markup for trending posts
          this.trendingData = `
            <div class="post-trending-articles content-wrap embed_custom">
                <h2 class="section-head">
                    <span>${this.bbgiconfigData.trending_article_title}</span>
                </h2>
                <div class="archive-tiles -list">
                    ${trendingPosts.join('')}
                </div>
            </div>
          `;
  
          // Render the trending posts HTML based on shortcode and location attributes
          if (has_shortcode) {
            if (location === 'embed_custom') {
              this.elements[i].innerHTML = this.trendingData;
            }
          } else {
            if (this.bbgiconfigData.isTrendingPostRender[location] === false) {
              if (location === 'embed_inner_listicle') {
                this.bbgiconfigData.isTrendingPostRender[location] = false;
              } else if (location === 'embed_inner_gallery') {
                this.bbgiconfigData.isTrendingPostRender[location] = false;
              } else if (location === 'embed_inner_AM') {
                this.bbgiconfigData.isTrendingPostRender[location] = false;
              } else {
                this.bbgiconfigData.isTrendingPostRender[location] = true;
              }
              this.elements[i].innerHTML = this.trendingData;
            }
          }
        } catch (error) {
          console.error(error);
        }
      }
    }
  
    // Asynchronously fetches posts from the API endpoint
    async fetchPostsEndpoint(url) {
      let obj;
      let data;
  
      // Fetch the initial data from the endpoint URL
      const res = await fetch(this.endpointURL);
      obj = await res.json();
      let transformedURL = obj.url;
      transformedURL = transformedURL.replace('{url}', url);
  
      // Fetch the actual post data from the transformed URL
      const result = await fetch(transformedURL);
      data = await result.json();
  
      return data;
    }
  
    // Removes 'https://' or 'http://' from a URL
    normalizeUrl(urlToNormalize) {
      return urlToNormalize.replace('https://', '').replace('http://', '');
    }
  
    // Converts text into a URL-friendly format
    slugify(text) {
      return text
        .toString()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
    }
  
    // Generates HTML markup for a post
    renderPostData(slug, normalizedUrl, title, pubDate) {
      const targetUrl = `https://${normalizedUrl}`;
      return `<div class="post-tile post"><div class="post-details"><div class="post-title"><p><a href="${targetUrl}">${title}</a></p></div></div></div>`;
    }
  }
  
  // Create an instance of the TrendingArticleModule class
  const trendingModule = new TrendingArticleModule();
  
  // Call the fetchPosts method to initiate fetching and rendering of posts
  trendingModule.fetchPosts();
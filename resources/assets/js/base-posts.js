module.exports = {
    props: ['type'],

    /**
     * The component's data.
     */
    data() {
        return {
            posts: [],
        };
    },

    /**
     * Prepare the component.
     */
    mounted() {
        this.getPosts();
    },

    methods: {
        /**
         * Get the type's posts
         */
        getPosts() {
            axios.get(`/base/${Spark.locale}/posts?type=${this.type}`).then(response => {
                this.posts = response.data.data;
            });
        },
    },
};

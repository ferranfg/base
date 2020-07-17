module.exports = {
    props: ['type'],

    /**
     * The component's data.
     */
    data() {
        return {
            tags: [],
        };
    },

    /**
     * Prepare the component.
     */
    mounted() {
        this.getTags();
    },

    methods: {
        /**
         * Get the type's tags
         */
        getTags() {
            axios.get(`/base/${Spark.locale}/tags?type=${this.type}`).then(response => {
                this.tags = response.data.data;
            });
        },
    },
};

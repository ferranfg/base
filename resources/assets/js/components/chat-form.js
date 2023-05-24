Vue.component('chat-form', {

    props: ['defaultInput'],

    data() {
        return {
            chatForm: new SparkForm({
                input: ''
            }),
            currentInput: this.defaultInput,
            rawResponse: '',
            typingResponse: '',
        };
    },

    mounted() {
        if (this.defaultInput) {
            this.chatForm.input = this.defaultInput;
            this.submit();
        }
    },

    methods: {
        submit() {
            this.resetResponse();
            this.currentInput = this.chatForm.input;

            Spark.post('/chat', this.chatForm).then(response => {
                this.rawResponse = response.text;
                this.startTyping();

                this.chatForm.reset();
            }).catch(error => {
                this.currentInput = false;
            });
        },

        resetResponse() {
            this.rawResponse = '';
            this.typingResponse = '';
        },

        startTyping() {
            this.typingResponse = this.rawResponse.substring(0, this.typingResponse.length + 1);

            if (this.typingResponse.length != this.rawResponse.length) {
                setTimeout(this.startTyping, 20);
            }
        }
    }
});
import Vue from '$vue';
import { SparkForm } from '../forms/form';

Vue.component('chat-form', {

    props: ['defaultInput'],

    data() {
        return {
            chatForm: new SparkForm({
                input: this.defaultInput
            }),
            showInput: this.defaultInput != '',
            response: null
        };
    },

    mounted() {
        if (this.chatForm.input) this.submit();
    },

    methods: {
        submit() {
            this.showInput = true;
            Spark.post('/chat', this.chatForm).then(response => {
                this.response = response.text;
            });
        }
    }
});
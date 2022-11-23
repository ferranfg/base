import Vue from '$vue';

Vue.component("request-access", {
    props: ["supportForm", "showSupportRequestSuccessMessage"],

    methods: {
        sendAccessRequest() {
            this.supportForm.subject = `Request Access from: ${this.supportForm.from}`;
            this.supportForm.message = `Request Access from: ${this.supportForm.from}`;

            Spark.post("/request-access", this.supportForm).then(() => {
                $("#modal-support").modal("hide");

                this.showSupportRequestSuccessMessage();
                this.supportForm.reset();
                this.mounted();
            });
        }
    }
});
<chat-form default-input="{{ isset($input) ? $input : '' }}" inline-template>
    <div class="position-relative">
        <div v-if="currentInput" class="position-absolute" style="right:0">
            <button type="button" class="btn btn-light btn-sm" v-on:click="resetAll">
                <span class="fa fa-sync"></span>
            </button>
        </div>
        <div class="media mb-3 pr-5 text-muted" v-if="currentInput">
            <div class="rounded-circle border p-1 mr-2 text-center" style="width:34px">
                <span class="fa fa-user"></span>
            </div>
            <div class="media-body">
                <p class="mb-0">@{{ currentInput }}</p>
            </div>
        </div>
        <div class="media mb-3" v-if="showTyping">
            <div class="rounded-circle border p-1 mr-2 text-center" style="width:34px">
                <span class="fa fa-spinner fa-spin"></span>
            </div>
            <div class="media-body">
                <p class="mb-0">{{__('Typing...')}}</p>
            </div>
        </div>
        <div class="media mb-3" v-if="rawResponse">
            <div class="rounded-circle border p-1 mr-2 text-center" style="width:34px">
                <span class="fas fa-robot"></span>
            </div>
            <div class="media-body" v-html="typingResponse"></div>
        </div>
        <div class="subcribe-form chat-form" v-if="!currentInput">
            <form class="my-0">
                <div class="form-group mb-0" :class="{'is-invalid': chatForm.errors.has('input')}">
                    <textarea name="input" class="rounded" v-model="chatForm.input" ref="input" placeholder="{{__('Hello! How can I help?')}}" :disabled="chatForm.busy" rows="2"></textarea>
                    <button type="button" class="btn btn-primary" @click.prevent="submit" :disabled="chatForm.busy">
                        <span class="fa fa-paper-plane"></span>
                    </button>
                </div>
                <span class="invalid-feedback" v-show="chatForm.errors.has('input')">
                    @{{ chatForm.errors.get('input') }}
                </span>
            </form>
        </div>
    </div>
</chat-form>
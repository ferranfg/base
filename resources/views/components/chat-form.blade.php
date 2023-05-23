<chat-form default-input="{{ isset($input) ? $input : '' }}" inline-template>
    <div>
        <div class="media mb-3" v-if="currentInput">
            <div class="rounded-circle border p-1 mr-2 text-center" style="width:34px">
                <span class="fa fa-user"></span>
            </div>
            <div class="media-body">
                <p class="mb-0">@{{ currentInput }}</p>
            </div>
        </div>
        <div class="media mb-3 text-white" v-if="currentInput && !rawResponse">
            <div class="rounded-circle border p-1 mr-2 text-center" style="width:34px">
                <span class="fa fa-spinner fa-spin"></span>
            </div>
            <div class="media-body">
                <p class="mb-0">{{__('Escribiendo...')}}</p>
            </div>
        </div>
        <div class="media mb-3 text-white" v-if="rawResponse">
            <div class="rounded-circle border p-1 mr-2 text-center" style="width:34px">
                <span class="fas fa-robot"></span>
            </div>
            <div class="media-body" v-html="typingResponse"></div>
        </div>
        <div class="subcribe-form">
            <form class="m-0">
                <div class="form-group mb-0" :class="{'is-invalid': chatForm.errors.has('input')}">
                    <input type="text" name="input" class="rounded" v-model="chatForm.input" placeholder="{{__('Hola! ¿Cuál es tu pregunta?')}}" :disabled="chatForm.busy">
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
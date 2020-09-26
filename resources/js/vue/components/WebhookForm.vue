<template>
    <div>
        <b-form v-if="show" :action="`/webhooks/${form.id || ''}`" method="POST">
            <input type="hidden" name="_method" :value="!form.id ? 'POST' : 'PUT'">
            <input type="hidden" name="_token" :value="token">
            <b-form-group
                id="input-group-1"
                label="Name:"
                label-for="input-1"
                description="Please provide webhook name"
            >
                <b-form-input
                    id="input-1"
                    v-model="form.name"
                    name="name"
                    required
                    placeholder="Enter webhook name"
                ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-2" label="Your slug for incoming URL endpoint:" label-for="input-2">
                <b-form-input
                    id="input-2"
                    v-model="form.in"
                    name="in"
                    required
                    placeholder="Enter slug of URL for getting your webhooks"
                ></b-form-input>
                <span v-if="fullUrl">
                    <b>Full URL: </b><br><a :href="fullUrl">{{ fullUrl }}</a>
                </span>
            </b-form-group>

            <b-form-group id="input-group-3" label="Your outcoming full URL endpoint:" label-for="input-2">
                <b-form-input
                    id="input-2"
                    v-model="form.out"
                    name="out"
                    required
                    placeholder="Enter full URL for proxying your webhooks"
                ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-5" label="Your outcoming full URL endpoint:" label-for="input-2"
                          description="It's will proxied only when you are here in the page. Note: Make sure your local project allowing CORS">
                <b-form-input
                    id="input-5"
                    v-model="form.out_local"
                    name="out_local"
                    required
                    placeholder="Enter local full URL for proxying your webhooks in the page"
                ></b-form-input>
            </b-form-group>

            <b-button v-b-toggle.collapse-default-headers variant="primary">Set default headers</b-button>
            <b-collapse id="collapse-default-headers" class="mt-2">
                <b-form-group id="input-group-4" label="Default headers" label-for="input-3" description="Default header will merged/assigned to your outcoming requests">
                    <v-jsoneditor v-model="form.default_headers" :options="{mode: 'code'}" :plus="false" height="400px"/>
                    <input type="hidden" name="default_headers" v-model="default_headers">
                </b-form-group>
                <br>
            </b-collapse>
            <b-button v-b-toggle.collapse-default-payload variant="primary">Set default headers</b-button>
            <b-collapse id="collapse-default-payload" class="mt-2">
                <b-form-group id="input-group-6" label="Default payload" label-for="input-3" description="Default payload will merged/assigned to your outcoming requests">
                    <v-jsoneditor v-model="form.default_payload" :options="{mode: 'code'}" :plus="false" height="400px"/>
                    <input type="hidden" name="default_payload" v-model="default_payload">
                </b-form-group>
                <br>
            </b-collapse>

            <b-button type="submit" variant="primary">Save</b-button>
            <b-button v-if="form.id" v-b-toggle.collapse-1 variant="primary">Send request</b-button>
        </b-form>
        <b-collapse id="collapse-1" class="mt-2">
            <b-card>
                <http-request-sender></http-request-sender>
            </b-card>
        </b-collapse>
        <vue-terminal v-if="form.id" :intro="terminalText" :key="rerender" style="pointer-events: none; margin-top: 15px;"></vue-terminal>
    </div>
</template>

<script>
    import HttpRequestSender from './HttpRequestSender.vue'
    import VueTerminal from 'vue-terminal-ui'

    export default {
        components: {HttpRequestSender, VueTerminal},
        props: {
            baseUrl: {
                type: String,
                required: true
            },
            userId: {
                type: Number,
                required: true
            },
            userBroadcastingToken: {
                type: String,
                required: true
            },
            webhook: {
                type: Object,
                required: false,
                default: () => ({})
            }
        },
        created () {
            if (typeof this.webhook.default_headers === "string") {
                this.webhook.default_headers = JSON.parse(this.webhook.default_headers)
            }
            if (typeof this.webhook.default_payload === "string") {
                this.webhook.default_payload = JSON.parse(this.webhook.default_payload)
            }
            this.form = this.webhook

            if (this.form.id) {
                console.log(this.webhook.channel)
                Echo.channel(this.webhook.channel)
                    .listen('WebhookCallEvent', (e) => {
                        this.addNewLineToTerminal(`Method: ${e.method}`)
                        this.addNewLineToTerminal(`URL: ${e.url}`)
                        this.addNewLineToTerminal(`Payload: ${JSON.stringify(e.payload)}`)
                        this.addNewLineToTerminal(`________________________________________________________________________`)

                        this.makeProxyRequest(e)
                    })
            }

        },
        data() {
            return {
                rerender: 0,
                terminalText: '',
                form: {
                    name: '',
                    in: null,
                    out: null,
                    default_headers: {},
                    default_payload: {}
                },
                foods: [{ text: 'Select One', value: null }, 'Carrots', 'Beans', 'Tomatoes', 'Corn'],
                show: true
            }
        },
        beforeDestroy() {
            if (!this.form.id) {
                return
            }
            Echo.leaveChannel('webhooks.' + this.form.id)
        },
        computed: {
            token() {
                return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            default_headers () {
                return JSON.stringify(this.form.default_headers)
            },
            default_payload () {
                return JSON.stringify(this.form.default_payload)
            },
            fullUrl () {
                return this.form.in ? `${this.baseUrl}/api/webhook/endpoints/${this.userBroadcastingToken}/${this.form.in}` : null
            }
        },
        methods: {
            async makeProxyRequest ({headers, payload, url, method, webhook}) {
                try {
                    if (method.toLowerCase() === 'get') {
                        axios[method.toLowerCase()](webhook.out_local, headers)
                        return
                    }
                    axios[method.toLowerCase()](webhook.out_local, payload, headers)
                } catch (e) {

                }
            },
            addNewLineToTerminal (newLine) {
                this.terminalText += newLine + '<br>\n'
                ++this.rerender
            }
        }
    }
</script>

<template>
    <div>
        <b-form-group
            id="input-group-0"
            label="Method:"
            label-for="input-0"
            description="Request method"
        >
            <b-form-select v-model="request.method" :options="[
                {
                    value: 'get',
                    text: 'GET',
                },
                {
                    value: 'delete',
                    text: 'DELETE',
                },
                {
                    value: 'PUT',
                    text: 'PUT',
                },
                {
                    value: 'patch',
                    text: 'PATCH',
                },
                {
                    value: 'post',
                    text: 'POST',
                },
            ]"></b-form-select>
        </b-form-group>
        <b-form-group
            id="input-group-1"
            label="Url:"
            label-for="input-1"
            description="Please provide URL for request"
        >
            <b-form-input
                id="input-1"
                v-model="request.url"
                name="name"
                required
                placeholder="Enter URL"
            ></b-form-input>
        </b-form-group>
        <b-button v-b-toggle.collapse-2 variant="primary">Set headers</b-button>
        <b-collapse id="collapse-2" class="mt-2">
            <b-form-group id="input-group-4" label="Headers" label-for="input-3" description="">
                <v-jsoneditor
                    v-model="request.headers" :options="{mode: 'code'}"
                    :plus="false"
                    height="400px"
                />
            </b-form-group>
            <br>
        </b-collapse>

        <b-button v-b-toggle.collapse-3 variant="primary">Set payload</b-button>
        <b-collapse id="collapse-3" class="mt-2">
            <b-form-group id="input-group-6" label="Payload" label-for="input-3" description="">
                <v-jsoneditor
                    v-model="request.payload"
                    :options="{mode: 'code'}"
                    :plus="false"
                    height="400px"
                />
            </b-form-group>
            <br>
        </b-collapse>

        <b-button @click.prevent="send" type="submit" variant="primary">Send</b-button>
        <hr v-if="response">
        <div v-if="response">
            <h2>Response</h2>
            <b-form-group id="input-group-7" label="Response" label-for="input-3" description="Response result">
                <v-jsoneditor
                    v-model="response" :options="{mode: 'code'}"
                    :plus="false"
                    height="600px"
                />
            </b-form-group>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'HttpRequestSender',
        data() {
            return {
                request: {
                    headers: {},
                    payload: {},
                    url: 'http://localhost:3000/api/write/log',
                    method: 'post'
                },
                response: null
            }
        },
        methods: {
            async send() {
                if (this.request.method === 'get') {
                    this.response = await axios[this.request.method](this.request.url, this.request.headers)
                    return
                }
                this.response = await axios[this.request.method](this.request.url, this.request.payload, this.request.headers)
            }
        }
    }
</script>

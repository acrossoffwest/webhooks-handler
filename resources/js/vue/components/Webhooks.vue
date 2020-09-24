<template>
    <div>
        <div class="row" v-if="models" v-for="model in models">
            <div class="col-12">
                <b>{{ model.name }}</b>:<br>
                <p><a :href="model.endpoint">{{ model.endpoint }}</a> -> <a :href="model.out">{{ model.out }}</a></p>
                <p><a :href="`/webhooks/${model.id}`">Open</a></p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Webhooks",
        props: {
            baseUrl: {
                type: String,
                required: true
            },
            userId: {
                type: Number,
                required: true
            }
        },
        async created () {
            await this.loadWebhooks()
        },
        data() {
            return {
                models: null
            }
        },
        computed: {
            authorized () {
                return !!axios.defaults.headers.common['Authorization']
            },
            headers () {
                return {
                    Authorization: window.axios.defaults.headers.common['Authorization']
                }
            }
        },
        methods: {
            fullUrl (slug) {
                return slug ? `${this.baseUrl}/api/webhook/endpoints/${this.userId}/${slug}` : null
            },
            async loadWebhooks () {
               const {data: {data}} = await axios.get('api/webhooks')
               this.models = data
            }
        }
    }
</script>

<style scoped>

</style>

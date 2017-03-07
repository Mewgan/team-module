<template>
    <div>

    </div>
</template>

<script type="text/babel">
    export default{
        name: 'user-price',
        props: {
            line: {
                default: 'default'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                default: null
            },
            website: {
                required: true
            }
        }
    }
</script>
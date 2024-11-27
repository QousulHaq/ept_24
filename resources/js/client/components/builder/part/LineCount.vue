<template>
  <div id="line-count">
    <div :key="`line-count-`+i" v-for="(style, i) in context.items" :style="style">{{ i + 1 }}</div>
  </div>
</template>

<script>
  export default {
    name: "LineCount",
    props: {
      context: {
        type: Object,
        default: () => ({
          items: []
        })
      }
    }
  }
</script>

<style scoped>
  #line-count {
    position: absolute;
    width: 18px;
  }

  #line-count div {
    width: 18px;
    color: #f1f3fe;
    font-size: small;
    text-align: right;
  }
</style>

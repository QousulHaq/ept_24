<template>
  <div class="card">
    <div class="card-body">
      <div class="form-group">
        <label for="question_code">Question Code</label>
        <input id="question_code" type="text" class="form-control" :value="value" @change="questionCodeForm"
               :disabled="disabled">
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "QuestionCodeForm",
    props: {
      value: {
        default: ''
      },
      disabled: {
        default: false
      }
    },
    methods: {
      questionCodeForm: _.debounce(function (e) {
        this.$emit('input', e.target.value)
      }, 500)
    }
  }
</script>

<style scoped>

</style>

<template>
  <div v-if="item !== null">
    <bundle v-if="item.type === 'bundle'" :item="item"
            @onCountdownFreezeChange="args => $emit('onCountdownFreezeChange', args)">
      <template #single="{ item: subItem, value: subItemValue }">
        <multi-choice-single :item="subItem" :disable-plugin="subItem.id !== item.id" :music-icon="{ width: 60, height: 40, hidden: true }"
                             @onCountdownFreezeChange="args => $emit('onCountdownFreezeChange', args)" :style="{ marginTop: '2em' }">
          <a-radio-group @change="e => answerChange(e, subItem.id)" :value="multipleChoiceValue(subItem, subItemValue)"
                         :disabled="disabled">
            <a-radio-button v-for="(answer, i) in subItem.answers" :key="answer.id" :value="answer.id">
              {{String.fromCharCode(65 + i)}}. {{answer.content}}
            </a-radio-button>
          </a-radio-group>
        </multi-choice-single>
      </template>
    </bundle>
    <multi-choice-single v-else-if="item.type === 'multi_choice_single'" :item="item"
                         @onCountdownFreezeChange="args => $emit('onCountdownFreezeChange', args)">
      <a-radio-group @change="e => answerChange(e)" :value="multipleChoiceValue()" :disabled="disabled">
        <a-radio-button v-for="(answer, i) in item.answers" :key="answer.id" :value="answer.id">
          {{String.fromCharCode(65 + i)}}. {{answer.content}}
        </a-radio-button>
      </a-radio-group>
    </multi-choice-single>
  </div>
</template>

<script>
  import Bundle from '../builder/Bundle'
  import MultiChoiceSingle from '../builder/MultiChoiceSingle'

  export default {
    name: "QuizBuilder",
    props: {
      item: {
        type: Object,
        required: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
      value: {
        type: String,
        default: ''
      }
    },
    components: { Bundle, MultiChoiceSingle },
    methods: {
      answerChange (element, itemId = null) {
        this.$emit('change', { value: element.target.value, itemId })
      },
      multipleChoiceValue: function (item = null, value = null) {
        return (item ?? this.item).answers.find(a => a.content === (value ?? this.value))?.id ?? ''
      }
    }
  }
</script>

<!--suppress CssUnusedSymbol -->
<style scoped>
  .ant-radio-group {
    width: 100%;
  }

  .ant-radio-button-wrapper {
    display: block;
    margin: 0.5em 0;
    height: auto;
    color: rgba(0, 0, 0, 0.9);
  }
</style>

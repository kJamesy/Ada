'use strict';

import { Pie, mixins } from 'vue-chartjs';
const { reactiveProp } = mixins;

export default {
    extends: Pie,
    mixins: [reactiveProp],
    props: ['options', 'width', 'height'],
    mounted () {
        this.renderChart(this.chartData, this.options)
    },
    watch: {
        options(newVal, oldVal)  {
            if ( ! _.isEqual(newVal, oldVal) ) {
                if ( typeof newVal.title.text === 'string' )
                    this.$data._chart.options.title.text = newVal.title.text;
            }
        }
    }
}
var LineScoreComponent = {
  name: 'LineScoreComponent',
  template: `
    <div class="container">
      <b-row>
        <b-col cols="12">
          <!--
          <b-table small bordered no-border-collapse :fields="fields" :items="lineScore">
          -->
          <b-table small borderless head-variant="light" :fields="fields" :items="lineScore" class="lineScoreTable" style="padding: .2rem">
          </b-table>
        </b-col>
      </b-row>
    </div>
  `,
  props: ['gameInfo'],
  data: function() {
    return {
      fields: [
        { key: 'team', label: '' },
        { key: 'separator1', label: '' },
        { key: 'inning1', label: '1' },
        { key: 'inning2', label: '2' },
        { key: 'inning3', label: '3' },
        { key: 'separator4', label: '' },
        { key: 'inning4', label: '4' },
        { key: 'inning5', label: '5' },
        { key: 'inning6', label: '6' },
        { key: 'separator7', label: '' },
        { key: 'inning7', label: '7' },
        { key: 'inning8', label: '8' },
        { key: 'inning9', label: '9' },
        { key: 'separatorEnd', label: '' },
        { key: 'runs', label: 'R' },
        { key: 'hits', label: 'H' },
        { key: 'errors', label: 'E' },
      ],
      lineScore: [
      ],
    }
  },
  watch: {
    gameInfo() {
      this.updateLineScore();
    },
  },
  mounted() {
    this.updateLineScore();
  },
  methods: {
    updateLineScore() {
      //console.log(this.gameInfo);
      if (this.gameInfo == null) return;
      sit = this.gameInfo.situation.situation;
      lineScore = [];
      lineScore.push({team: this.gameInfo.visitor.name.toUpperCase(), separatorBegin: '', separatorEnd: '', runs: sit.runs[0], hits: sit.hits[0], errors: sit.errors[0]});
      lineScore.push({team: this.gameInfo.home.name.toUpperCase(), separatorBegin: '', separatorEnd: '', runs: sit.runs[1], hits: sit.hits[1], errors: sit.errors[1]});
      inning = sit.inning;
      if (inning < 9) inning = 9;
      fields = [ { key: 'team', label: '' }, { key: 'separatorBegin', label: ''} ]; 
      for (let i=(inning-9); i<inning; i++) {
        if ((i != (inning - 9)) && (i%3 == 0)) {
          fields.push({ key: 'separator' + (i+1), label: '' });
          lineScore[0]['separator' + (i+1)] = '';
          lineScore[1]['separator' + (i+1)] = '';
        }
        fields.push({ key: 'inning' + (i+1), label: (i+1).toString() });
        if (i <= sit.inning) {
          lineScore[0]['inning' + (i+1)] = sit.runsPerInning[0][i];
        } else {
          lineScore[0]['inning' + (i+1)] = ' ';
        }
        if (i < sit.inning || (i == sit.inning && sit.side == 1)) {
          lineScore[1]['inning' + (i+1)] = sit.runsPerInning[1][i];
        } else {
          lineScore[1]['inning' + (i+1)] = ' ';
        }
      }
      fields.push({ key: 'separatorEnd', label: '' });
      fields.push({ key: 'runs', label: 'R' });
      fields.push({ key: 'hits', label: 'H' });
      fields.push({ key: 'errors', label: 'E' });
      this.lineScore = lineScore;
      this.fields = fields;
    },
  },
}



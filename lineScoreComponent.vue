var LineScoreComponent = {
  name: 'LineScoreComponent',
  template: `
    <div class="container">
<!--
	  {{ lineScore }}
-->
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
		{ key: 'seperator1', label: '' },
		{ key: 'inning1', label: '1' },
		{ key: 'inning2', label: '2' },
		{ key: 'inning3', label: '3' },
		{ key: 'seperator4', label: '' },
		{ key: 'inning4', label: '4' },
		{ key: 'inning5', label: '5' },
		{ key: 'inning6', label: '6' },
		{ key: 'seperator7', label: '' },
		{ key: 'inning7', label: '7' },
		{ key: 'inning8', label: '8' },
		{ key: 'inning9', label: '9' },
		{ key: 'seperatorEnd', label: '' },
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
	  if (this.gameInfo == null) return;
	  sit = this.gameInfo.situation.situation;
	  lineScore = [];
	  lineScore.push({team: this.gameInfo.visitor.name.toUpperCase(), seperatorEnd: '', runs: sit.runsV, hits: sit.hitsV, errors: sit.errorsV});
	  lineScore.push({team: this.gameInfo.home.name.toUpperCase(), seperatorEnd: '', runs: sit.runsH, hits: sit.hitsH, errors: sit.errorsH});
	  inning = sit.inning;
	  if (inning < 9) inning = 9;
	  fields = [ { key: 'team', label: '' } ]; 
	  for (let i=0; i<inning; i++) {
	    if (i%3 == 0) {
              fields.push({ key: 'seperator' + (i+1), label: '' });
              lineScore[0]['seperator' + (i+1)] = '';
              lineScore[1]['seperator' + (i+1)] = '';
            }
	    fields.push({ key: 'inning' + (i+1), label: (i+1).toString() });
            lineScore[0]['inning' + (i+1)] = ' ';
            lineScore[1]['inning' + (i+1)] = ' ';
	  }
	  fields.push({ key: 'seperatorEnd', label: '' });
	  fields.push({ key: 'runs', label: 'R' });
	  fields.push({ key: 'hits', label: 'H' });
	  fields.push({ key: 'errors', label: 'E' });
	  this.lineScore = lineScore;
	  this.fields = fields;
	},
  },
}



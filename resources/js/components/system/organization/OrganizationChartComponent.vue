<template>
  <div>
    <highcharts :options="chartOptions"></highcharts>
  </div>
</template>

<script>
  export default {
    props: ['tree'],
    data() {
      return {
        chartOptions: {
          chart: {
            //height: '100%',
            height: 600,
            inverted: true
          },
          title: {
            useHTML: true,
            text: this.trans.get('keys.so_do_cay_co_cau_to_chuc')
          },
          accessibility: {
            point: {
              descriptionFormatter: function (point) {
                var nodeName = point.toNode.name,
                  nodeId = point.toNode.id,
                  nodeDesc = nodeName === nodeId ? nodeName : nodeName + ', ' + nodeId,
                  parentDesc = point.fromNode.id;
                return point.index + '. ' + nodeDesc + ', reports to ' + parentDesc + '.';
              }
            }
          },
          series: [
            {
              type: 'organization',
              name: 'Easia',
              keys: ['from', 'to'],
              data: [

                // ["PHH Group", "Easia"],
                // ["Easia", "Easia-BOM"],
                // ["Easia", "Easia-LEGAL & COMPLIANCE"],
                // ["Easia", "Easia-ACCOUNTING & FINANCE"],
                // ["Easia", "Easia-SALES"],
                // ["Easia", "Easia-OPERATION"],
                // ["Easia", "Easia-RESERVATION"],
                // ["Easia", "Easia-TICKETING & VISA & TRANSPORTATION"],

                // ["Easia", "Easia-IT"],
                // ["Easia", "Easia-MARKETING & BRANDING"],
                // ["Easia", "Easia-CONTRACTING"],
                // ["Easia", "Easia-DATABASE"],
                // ["Easia", "Easia-PROJECT"],
                // ["Easia", "Easia-HUMAN RESOURCES & ADMIN"],
                // ["Easia", "Easia-PRODUCTION RESEARCH & DEVELOPMENT CONSULTANT"],
                // ["Easia", "Easia-PRODUCTION"],
                // ["Easia", "Easia-TRAINING & DEVELOPMENT"],
                // ["Easia", "Easia-ACCOUNTING"],
                // ["PHH Group", "TVE"],
                // ["TVE", "112221121"],
                // ["TVE", "2312312"],
                // ["TVE", "2121212c"],
                // ["2121212c", "XYZ"],
                // ["2121212c", "ABC"],
                // ["PHH Group", "Exotic"],
                // ["Exotic", "Exotic-Sales & Marketing"],
                // ["Exotic", "Exotic-Sales"],
                // ["Exotic", "Exotic-Marketing"],
                // ["Exotic", "Exotic-Operation "],
                // ["Exotic", "Exotic-Accounting"],
                // ["PHH Group", "Begodi"],
                // ["Begodi", "Begodi-Director"],
                // ["Begodi", "Begodi-Russian Market"],
                // ["Begodi", "Begodi-Marketing"],
                // ["Begodi", "Begodi-Sales"],
                // ["Begodi", "Begodi-Contracting"],
                // ["Begodi", "Begodi-Accounting "],
                // ["Begodi", "Begodi-Salas"],
                // ["PHH Group", "AVANA"]

                // ['Shareholders', 'Board'],
                // ['Board', 'CEO'],
                // ['CEO', 'CTO'],
                // ['CEO', 'CPO'],
                // ['CEO', 'CSO'],
                // ['CEO', 'CMO'],
                // ['CEO', 'HR'],
                // ['CTO', 'Product'],
                // ['CTO', 'Web'],
                // ['CSO', 'Sales'],
                // ['CMO', 'Market']

              ],
              levels: [
                {
                  level: 0,
                  color: 'purple',
                  dataLabels: {
                    color: 'white'
                  },
                  height: 25
                },
                {
                  level: 1,
                  color: 'black',
                  dataLabels: {
                    color: 'white'
                  },
                  height: 25
                },
                {
                  level: 2,
                  color: '#9e226d',
                  dataLabels: {
                    color: 'white',
                  },
                },
                {
                  level: 3,
                  color: '#3aaa59'
                },
                {
                  level: 4,
                  color: '#d46c13'
                }
              ],
              nodes: [],
              colorByPoint: false,
              color: '#007ad0',
              dataLabels: {
                color: 'white'
              },
              borderColor: 'white',
              nodeWidth: 65
            }
          ],
          tooltip: {
            outside: true
          },
          exporting: {
            allowHTML: true,
            sourceWidth: 800,
            sourceHeight: 600
          }
        }
      };
    },
    methods: {
      fetchTree(list, outputs, nodes) {
        for (const [key, item] of Object.entries(list)) {

          if (item.level === 1) { //Nếu là level 1 => trực thuộc PHH group
            outputs.push(['PHH Group', item.code]); //Use code instead of name to avoid auto group item by library
          }

          let hasNode = false;

          //Node sử dụng khi muốn set riêng style: layout hanging, font size etc..
          let newNode = {
            id: item.code
          };

          if (item.code.length > 40) { //tên quá dài => chọn font chữ nhỏ
            hasNode = true;
            newNode.dataLabels = {
              style: {
                fontSize: "smaller"
              }
            };
          }

          if (item.children.length > 0) { //Nếu có con
            if (item.level <= 1) { //Skip to level 2
              if (item.level % 2 !== 0) { //chọn layout hanging cho level 1, 3, 5, 7
                hasNode = true;
                newNode.layout = 'hanging';
              }
              for (const [key, child] of Object.entries(item.children)) {
                let newItemHasChild = [item.code, child.code];
                outputs.push(newItemHasChild);
              }
              this.fetchTree(item.children, outputs, nodes);
            }
          }

          //Thêm node nếu có
          if (hasNode === true) {
            nodes.push(newNode);
          }

        }
        return outputs;
      }
    },
    mounted() {
      let organizations = [];
      let nodes = [];
      //fetch tree data
      this.fetchTree(this.tree, organizations, nodes);
      //Set chart options
      this.chartOptions.series[0].data = organizations;
      this.chartOptions.series[0].nodes = nodes;
      //Set height for chart base on number of organizations
      if (organizations.length < 20) {
        this.chartOptions.chart.height = 1300;
        this.chartOptions.exporting.sourceHeight = 1300;
      } else if (organizations.length > 20 && organizations.length < 35) {
        this.chartOptions.chart.height = 1600;
        this.chartOptions.exporting.sourceHeight = 1600;
        this.chartOptions.exporting.sourceWidth = 1000;
      } else if (organizations.length > 35 && organizations.length < 50) {
        this.chartOptions.chart.height = 1900;
        this.chartOptions.exporting.sourceHeight = 1900;
        this.chartOptions.exporting.sourceWidth = 1000;
      } else if (organizations.length > 50) {
        this.chartOptions.chart.height = 2200;
        this.chartOptions.exporting.sourceHeight = 2200;
        this.chartOptions.exporting.sourceWidth = 1000;
      }
    }
  };
</script>

<style scoped>
  /* Not working => move to style.css */
</style>

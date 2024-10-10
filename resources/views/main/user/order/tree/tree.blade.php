<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D3.js Tree Chart</title>
    <script src="https://d3js.org/d3.v6.min.js"></script>
    <style>
        .node circle {
            fill: #999;
        }

        .node text {
            font: 10px sans-serif;
        }

        .node--internal circle {
            fill: #555;
        }

        .node--internal text {
            text-shadow: 0 1px 0 #fff;
        }

        .link {
            fill: none;
            stroke: #555;
            stroke-opacity: 0.4;
            stroke-width: 1.5px;
        }
    </style>
</head>
<body>
<svg style="margin-left: 50px;" width="1000" height="500"></svg>
<script>
    var svg = d3.select("svg"),
        width = +svg.attr("width"),
        height = +svg.attr("height"),
        g = svg.append("g").attr("transform", "translate(40,0)");

    var tree = d3.tree()
        .size([height - 160, width - 160]);

    var data = @json($orderHierarchy); // Convert PHP array to JSON

    var stratify = d3.stratify()
        .id(function(d) { return d.id; })
        .parentId(function(d) { return d.parent; });

    var root = stratify(data)
        .sort(function(a, b) { return (a.height - b.height) || a.id.localeCompare(b.id); });

    tree(root);

    var link = g.selectAll(".link")
        .data(root.descendants().slice(1))
        .enter().append("path")
        .attr("class", "link")
        .attr("d", function(d) {
            return "M" + d.y + "," + d.x
                + "C" + (d.parent.y + 100) + "," + d.x
                + " " + (d.parent.y + 100) + "," + d.parent.x
                + " " + d.parent.y + "," + d.parent.x;
        });

    var node = g.selectAll(".node")
        .data(root.descendants())
        .enter().append("g")
        .attr("class", function(d) {
            return "node" + (d.children ? " node--internal" : " node--leaf");
        })
        .attr("transform", function(d) {
            return "translate(" + d.y + "," + d.x + ")";
        });

    node.append("circle")
        .attr("r", 10);

    node.append("text")
        .attr("dy", "2em") // Shift text down below the node circle
        .attr("x", function(d) { return 0; }) // Center the text under the circle
        .style("text-anchor", "middle") // Ensure the text is centered
        .text(function(d) { return d.data.name; });

</script>
</body>
</html>

<STYLE type="text/css">

table {
    border: 1px solid green;
    border-collapse: collapse;
    width:100%;
}

table td {
    border: 1px solid green;
}

table td.shrink {
    white-space:nowrap
}
table td.expand {
    width: 99%
}
</STYLE>

<?php
  include 'mysql.php';

  $con=mysqli_connect($host, $user, $password, $db);
  // Check connection
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

  $result = mysqli_query($con,"SELECT * FROM Orders");

  echo "<table border='1'>
  <tr>
  <th>ID</th>
  <th>Phone</th>
  <th>Room</th>
  <th>Status</th>
  <th>Start Time</th>
  <th>End Time</th>
  <th>Action</th>
  <th>Custom Msg</th>
  </tr>";

  while($row = mysqli_fetch_array($result)) {
    $status = $row['Status'];
    $room = $row['Room'];
    $room = str_replace("<", "", $room);
    $room = str_replace(">", "", $room);
    echo "<tr>";
    echo "<td>" . $row['ID'] . "</td>";
    echo "<td>" . $row['Phone'] . "</td>";
    echo "<td>" . $room . "</td>";
    echo "<td>" . $status . "</td>";
    echo "<td>" . $row['StartTime'] . "</td>";
    echo "<td>" . $row['EndTime'] . "</td>";
    if ($status == $PENDING) {
    echo "<td><a href='update.php?id=" . $row['ID'] . "&status=$ACCEPTED'>ACCEPT</a> <a href='update.php?id=" . $row['ID'] . "&status=$REJECTED'>REJECT</a> <a href='update.php?id=" . $row['ID'] . "&status=$OUTSIDE_DELIVERY'>OUTSIDE</a>";
    } else if ($status == $ACCEPTED) {
      echo "<td><a href='update.php?id=" . $row['ID'] . "&status=$CANCELLED'>CANCEL</a> <a href='update.php?id=" . $row['ID'] . "&status=$DELIVERED'>DELIVERED</a>";
    } else {
      echo "<td></td>";
    }
    echo "<td><form name='input' action='update.php' method='get'><input type='text' name='message'><input type='hidden' name='id' value='" . $row['ID'] . "'><input type='submit' value='Submit'></form></td>";
    echo "</tr>";
  }
  echo "</table>";

  mysqli_close($con);
  $state = atomic_read_contents($lock);
  echo "System is currently $state<br />";
  $option_state = $state == $DISABLED ? $ENABLED : $DISABLED;
  echo "<a href='change_state.php?&state=$option_state'>" . substr($option_state, 0, strlen($option_state) - 1) . "</a>";
?>


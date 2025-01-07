use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseIdToTimetablesTable extends Migration
{
    public function up()
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropColumn('course_id');
        });
    }
} 
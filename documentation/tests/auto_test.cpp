#include <sys/types.h>
#include <sys/stat.h>
#include <sys/wait.h>
#include <dirent.h>
#include <unistd.h>
#include <stdlib.h>
#include <cstdlib>
#include <cerrno>
#include <cstring>
#include <iostream>
#include <fstream>
#include <vector>
#include <string>
#include <sstream>
#include <fcntl.h>
#include <algorithm>
#include <boost/algorithm/string.hpp>
using namespace std;



int main(int argc, char *argv[]) {
	
	ifstream testfile;
	testfile.open(argv[1]);
	int i = 1;
	
	if (testfile.is_open()) {
		
		cout << "====================================" << endl
		     << "     AUTOMATED TESTS USING curl     " << endl
		     << "====================================" << endl;
		
		while (testfile.good()) {
			string line;
			getline(testfile, line);
			
			// If line is a comment, skip it
			if (line[0] == '#') continue;
			
			// Find : into current parsed line
			size_t a, b, colpos = (a=line.find(' ')) < (b=line.find('\t')) ? a : b;
			if (colpos == string::npos) continue; // space or tab not found
			
			// Get url and expected result
			string url = line.substr(0, colpos);
			string exp = line.substr(colpos+1);
			
			// Get possible extra options for curl
			size_t pipepos = exp.find('|');
			string opts = "";
			if (pipepos != string::npos) {
				opts = exp.substr(pipepos+1);
				exp = exp.substr(0, pipepos);
			}
			
			// Trim
			boost::trim(url);
			boost::trim(exp);
			boost::trim(opts);
			
			// Execute curl
			cout << i <<". " << url << " " << opts << endl << "   expected = " << exp << endl;
			string command = "curl -k " + opts + " https://localhost/" + url + " > last_curl_output 2> last_curl_stderr";
			system(command.c_str());
			
			// Read curl output
			string curl_result;
			ifstream curlfile;
			curlfile.open("last_curl_output");
			if (testfile.is_open()) {
				curlfile >> curl_result;
			} else {
				cerr << "Cannot open last_curl_output" << endl;
				exit(1);
			}
			cout << "   obtained = " << curl_result << endl;
			
			// Check if expected result matches actual result
			if (curl_result != exp) {
				cerr << "====================================" << endl
				     << "   ERROR!                           " << endl
				     << "====================================" << endl;
				cerr << "Expected result does not match obtained result, in test number " << i << endl;
				exit(2);
			}
			
			// Keep track of test number
			i++;
		}
		
		cout << "====================================" << endl
		     << "   ALL TESTS SUCCESSFULLY PASSED!   " << endl
		     << "====================================" << endl;
		
		testfile.close();
	
	} else {
		cerr << "Cannot open test file" << endl;
	}
	
	
	return 0;
}

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
			size_t semipos = line.find(":");
			if (semipos == string::npos) continue; // : not found
			
			// Get url and expected result
			string url = line.substr(0, semipos);
			string exp = line.substr(semipos+1);
			
			// Convert expected result into integer
			size_t expected;
			sscanf(exp.c_str(), "%d", &expected);
			
			// Execute curl
			cout << i <<". curl " << url << endl << "   expected result = " << expected << endl;
			string command = "curl -k https://localhost/" + url + " > last_curl_output 2> last_curl_stderr";
			system(command.c_str());
			
			// Read curl output
			size_t curl_result;
			ifstream curlfile;
			curlfile.open("last_curl_output");
			if (testfile.is_open()) {
				curlfile >> curl_result;
			} else {
				cerr << "Cannot open last_curl_output" << endl;
				exit(1);
			}
			cout << "   curl result = " << curl_result << endl;
			
			// Check if expected result match with actual result
			if (curl_result != expected) {
				cerr << "====================================" << endl
				     << "   ERROR!                           " << endl
				     << "====================================" << endl;
				cerr << "Expected result does not match in test number " << i << endl;
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
